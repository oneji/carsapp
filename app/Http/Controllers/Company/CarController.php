<?php

namespace App\Http\Controllers\Company;

use App\Car;
use App\CarAttachment;
use App\Company;
use App\Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class CarController extends Controller
{
    /**
     * Get all cars from database.
     * 
     * @return \Illuminate\Http\Response
     */
    public function get($company_slug)
    {
        $boundCarsID = DB::table('car_driver')->where('active', 1)->pluck('car_id')->all();

        $notBoundCars = Company::where('slug', $company_slug)->with([
            'cars' => function($query) use ($boundCarsID){
                $query->select('cars.id as id', 'year', 'number', 'shape_name', 'brand_name', 'model_name', 'milage', 'vin_code', 'cover_image', 'type', 'reserved')
                        ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                        ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                        ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                        ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                        ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')    
                        ->whereNotIn('cars.id', $boundCarsID)->where('reserved', 0)                    
                        ->with([ 'drivers' => function($q) {
                            $q->where('active', 1)->get();
                        }])->get();
            }
        ])->first();
           
        $boundCars = Company::where('slug', $company_slug)->with([
            'cars' => function($query) use ($boundCarsID){
                $query->select('cars.id as id', 'year', 'number', 'shape_name', 'brand_name', 'model_name', 'milage', 'vin_code', 'cover_image', 'type', 'reserved')
                        ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                        ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                        ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                        ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                        ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')    
                        ->whereIn('cars.id', $boundCarsID)->where('reserved', 0)                     
                        ->with([ 'drivers' => function($q) {
                            $q->where('active', 1)->get();
                        }])->get();
            }
        ])->first();    

        $company = Company::where('slug', $company_slug)->with([
            'cars' => function($query) use ($boundCarsID){
                $query->select('cars.id as id', 'year', 'number', 'shape_name', 'brand_name', 'model_name', 'milage', 'vin_code', 'cover_image', 'type', 'reserved')
                        ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                        ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                        ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                        ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                        ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')->where('reserved', 0)                         
                        ->with([ 'drivers' => function($q) {
                            $q->where('active', 1)->get();
                        }])->get();
            }
        ])->first();        
        
        return response()->json([
            'company' => $company,
            'bindSelect' => $notBoundCars,
            'unbindSelect' => $boundCars
        ]);
    }

    /**
     * Store a newly created car to the database.
     * 
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\Reponse
     */
    public function store(Request $request, $company_slug) 
    {   
        // return response()->json($request->all());
        $company = Company::where('slug', $company_slug)->first();

        if($request->hasFile('cover_image')) {
            $coverImageFullName = $request->file('cover_image')->getClientOriginalName(); 
            $coverImagename = pathinfo($coverImageFullName, PATHINFO_FILENAME);
            $coverImagePath = $request->file('cover_image')->path();
            $coverImageExtension = $request->file('cover_image')->getClientOriginalExtension();
            $coverImageNameToStore = time().'.'.$coverImageExtension;
            $path = $request->file('cover_image')->move(public_path('/uploads/car_cover_images'), $coverImageNameToStore);  
            $coverImageNameToStore = 'uploads/car_cover_images/'.$coverImageNameToStore;
        } else {
            $coverImageNameToStore = null;
        }

        $car = new Car($request->all());
        $car->year = Carbon::parse($request->year)->year;
        $car->cover_image = $coverImageNameToStore;
        if($request->milage !== 'null') 
            $car->milage = $request->milage;

        if($request->engine_capacity !== null)
            $car->engine_capacity = str_replace(',', '.', $request->engine_capacity);
        else
            $car->engine_capacity = null;

        if($request->reserved === 'true')
            $car->reserved = 1;
        else 
            $car->reserved = 0;
            
        $car->save();       
        
        $company->cars()->attach($car->id);

        $carAttachments = array();
        if($request->hasFile('attachments')) {            
            foreach($request->attachments as $file) {
                $fileFullName = $file->getClientOriginalName(); 
                $filename = pathinfo($fileFullName, PATHINFO_FILENAME);
                $filePath = $file->path();
                $fileExtension = $file->getClientOriginalExtension();
                $fileNameToStore = uniqid().'.'.$fileExtension;
                $path = $file->move(public_path('/uploads/attachments/cars'), $fileNameToStore);  
                $fileNameToStore = 'uploads/attachments/cars/'.$fileNameToStore;    
                
                array_push($carAttachments, new CarAttachment([
                    'attachment' => $fileNameToStore,
                    'attachment_name' => $fileFullName,
                    'attachment_ext' => strtolower($fileExtension),
                    'car_id' => $car->id
                ]));
            }
        } else {
            $fileNameToStore = null;
        }        

        if($fileNameToStore !== null)
            $car->attachments()->saveMany($carAttachments);

        return response()->json([
            'success' => true,
            'message' => 'Машина успешно создана.',
            'files' => $carAttachments
        ]);
    }

    /**
     * Bind driver to a car.
     * 
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\Response 
     */
    public function bindDriver(Request $request)
    {
        $boundCarIds = DB::table('car_driver')->where('active', 1)->get();
        
        foreach($boundCarIds as $boundCar) {
            if($boundCar->car_id === $request->car_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'У автомобиля уже есть водитель.'
                ]);
            }
        }

        $car = Car::find($request->car_id);
        $car->drivers()->attach([
            $request->driver_id => [
                'active' => 1,
                'start_date' => Carbon::now()
            ]
        ]);        

        $driver = Driver::find($request->driver_id);
        $car->load([
            'drivers' => function($query) {
                $query->where('active', 1)->get();
            }
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Водитель успешно привязан.',
            'driver' => $driver,
            'boundCars' => $boundCarIds,
            'car' => $car
        ]);
    }

    public function unbindDriver(Request $request) 
    {
        $drivers = DB::table('car_driver')->where('car_id', $request->car_id)->update([
            'active' => 0,
            'end_date' => Carbon::now()
        ]);

        $car = Car::find($request->only('car_id'))->first();
        $car->reserved = 1;
        $car->save();

        return response()->json([
            'success' => true,
            'message' => 'Водитель успешно удален. Машина отправлена в резерв.'
        ]);
    }

    public function reserveCar(Request $request, $company_slug) 
    {
        $car = Car::find($request->only('car_id'))->first();
        $car->reserved = 1;
        $car->save();

        return response()->json([
            'success' => true,
            'message' => 'Автомобиль успешно отправлен в резерв.'
        ]);
    } 

    public function backFromReserve(Request $request, $company_slug)
    {        
        $car = Car::find($request->only('car_id'))->first();
        $car->reserved = 0;
        $car->save();

        $carCompany = DB::table('car_company')->where('car_id', $car->id)->update([
            'company_id' => $request->company_id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Автомобиль успешно удален из резерва.'
        ]);
    }
}
