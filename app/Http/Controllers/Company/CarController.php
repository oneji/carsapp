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
        $company = Company::where('slug', $company_slug)->with([
            'cars' => function($query) {
                $query->select('cars.id as id', 'year', 'number', 'shape_name', 'brand_name', 'model_name', 'milage', 'vin_code', 'cover_image')
                        ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                        ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                        ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                        ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                        ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
                        ->with('drivers')->get();
            }
        ])->first();
        
        return response()->json($company);
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
        $boundCarIds = DB::table('car_driver')->get();
        
        foreach($boundCarIds as $boundCar) {
            if($boundCar->car_id === $request->car_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'У автомобиля уже есть водитель.'
                ]);
            }
        }

        $car = Car::find($request->car_id);
        $car->drivers()->attach($request->driver_id);
        

        $driver = Driver::find($request->driver_id);

        return response()->json([
            'success' => true,
            'message' => 'Водитель успешно привязан.',
            'driver' => $driver,
            'boundCars' => $boundCarIds
        ]);
    }
}
