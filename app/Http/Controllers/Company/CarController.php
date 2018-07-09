<?php

namespace App\Http\Controllers\Company;

use App\Car;
use App\CarCard;
use App\CarAttachment;
use App\Company;
use App\Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class CarController extends Controller
{
    /**
     * Get all sold cars.
     */
    public function getSoldCars($company_slug)
    {
        $soldCars = Car::getSold($company_slug);
        return response()->json($soldCars);
    }

    /**
     * Get the whole car card info.
     * 
     * @param   string $sto_slug
     * @param   int $car_id
     * 
     * @return  \Illuminate\Http\Response
     */
    public function getCardInfo($company_slug, $car_id)
    {
        $car = Car::select('cars.*', 'shape_name', 'brand_name', 'model_name','engine_type_name', 'transmission_name')
                        ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                        ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                        ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                        ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                        ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
                        ->with('attachments', 'card.comments.user', 'card.defect_acts.defect_options.defect', 'card.defect_acts.equipment')
                        ->where('sold', 0)
                        ->where('cars.id', $car_id)->with([ 'drivers' => function($q) {
                            $q->where('active', 1)->get();
                        }])->first(); 

        return response()->json([
            'car' => $car
        ]);
    }

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
                $query->select('cars.*', 'shape_name', 'brand_name', 'model_name', 'transmission_name', 'engine_type_name')
                        ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                        ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                        ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                        ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                        ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')    
                        ->whereNotIn('cars.id', $boundCarsID)
                        ->where('reserved', 0)  
                        ->where('sold', 0)                    
                        ->with([ 'card', 'drivers' => function($q) {
                            $q->where('active', 1)->get();
                        }])->get();
            }
        ])->first();
           
        $boundCars = Company::where('slug', $company_slug)->with([
            'cars' => function($query) use ($boundCarsID){
                $query->select('cars.*', 'shape_name', 'brand_name', 'model_name', 'transmission_name', 'engine_type_name')
                        ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                        ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                        ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                        ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                        ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')    
                        ->whereIn('cars.id', $boundCarsID)
                        ->where('reserved', 0)  
                        ->where('sold', 0)                     
                        ->with([ 'card', 'drivers' => function($q) {
                            $q->where('active', 1)->get();
                        }])->get();
            }
        ])->first();    

        $company = Company::where('slug', $company_slug)->with([
            'cars' => function($query) use ($boundCarsID){
                $query->select('cars.*', 'shape_name', 'brand_name', 'model_name', 'transmission_name', 'engine_type_name')
                        ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                        ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                        ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                        ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                        ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
                        ->where('reserved', 0)
                        ->where('sold', 0)                         
                        ->with([ 'card', 'drivers' => function($q) {
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
        $car->year = $request->year;
        $car->cover_image = $coverImageNameToStore;
        $car->reserved = $request->reserved === 'true' ? 1 : 0;

        if($request->teh_osmotr_end_date !== null && $request->teh_osmotr_end_date !== 'null')
            $car->teh_osmotr_end_date = Carbon::parse($request->teh_osmotr_end_date);

        if($request->tint_end_date !== null && $request->tint_end_date !== 'null')
            $car->tint_end_date = Carbon::parse($request->tint_end_date);

        if($request->milage !== null && $request->milage !== 'null') 
            $car->milage = $request->milage;

        if($request->engine_capacity !== null && $request->engine_capacity !== 'null')
            $car->engine_capacity = str_replace(',', '.', $request->engine_capacity);
            
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
            'message' => 'Машина успешно создана.'
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

    public function edit(Request $request, $company_slug, $id)
    {
        $car = Car::find($id);
        return response()->json($car);
    }

    public function update(Request $request, $company_slug, $id) 
    {
        $car = Car::find($id);
        $car->year = (int) $request->year;
        $car->number = $request->number;
        $car->shape_id = $request->shape_id;
        $car->brand_id = $request->brand_id;
        $car->model_id = $request->model_id;

        if($request->milage !== 'null') 
            $car->milage = $request->milage;

        $car->vin_code = $request->vin_code;
        $car->type = (int) $request->type;  
        
        if($request->hasFile('cover_image')) {
            $coverImageFullName = $request->file('cover_image')->getClientOriginalName(); 
            $coverImagename = pathinfo($coverImageFullName, PATHINFO_FILENAME);
            $coverImagePath = $request->file('cover_image')->path();
            $coverImageExtension = $request->file('cover_image')->getClientOriginalExtension();
            $coverImageNameToStore = time().'.'.$coverImageExtension;
            $path = $request->file('cover_image')->move(public_path('/uploads/car_cover_images'), $coverImageNameToStore);  
            $coverImageNameToStore = 'uploads/car_cover_images/'.$coverImageNameToStore;
            File::delete(public_path($car->cover_image));
        } else {
            $coverImageNameToStore = null;
        }
        
        if($coverImageNameToStore !== null)
            $car->cover_image = $coverImageNameToStore;
        
        $car->engine_capacity = $request->engine_capacity;
        $car->engine_type_id = $request->engine_type_id;
        $car->transmission_id = $request->transmission_id;
        
        if($request->engine_capacity !== 'null' && $request->engine_capacity !== null)
            $car->engine_capacity = str_replace(',', '.', $request->engine_capacity);
        
        if($request->teh_osmotr_end_date !== null && $request->teh_osmotr_end_date !== 'null')
            $car->teh_osmotr_end_date = Carbon::parse($request->teh_osmotr_end_date);

        if($request->tint_end_date !== null && $request->tint_end_date !== 'null')
            $car->tint_end_date = Carbon::parse($request->tint_end_date);

        if($request->reserved === 'true')
            $car->reserved = 1;
        else 
            $car->reserved = 0;
            
        $car->save(); 

        return response()->json([
            'success' => true,
            'message' => 'Автомобиль успешно изменен.' 
        ]);
    }

    /**
     * Change car 'sold' status.
     * 
     * @param   \Illuminate\Http\Request
     */
    public function changeSoldStatus($company_slug, $car_id, $status) 
    {
        $car = Car::find($car_id);
        $car->sold = $status;
        $car->save();

        $message = '';
        if((int) $status === 1)
            $message = 'Машина успешно продана.';
        if((int) $status === 0)
            $message = 'Машина удалена из списка проданных.';

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Store a newly created car card to a database.
     * 
     * @param   string $company_slug
     * @param   int $car_id
     * 
     * @return  \Illuminate\Http\Response
     */
    public function createCard($company_slug, $car_id)
    {
        $car = Car::find($car_id);
        $card = CarCard::where('car_id', $car->id)->get();

        if(count($car) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Машины не существует. Попытка подмены данных.'
            ]);
        }
        
        if(count($card) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'У машины уже имеется карточка.'
            ]);
        }

        $card = new CarCard();
        $card->car_id = $car->id;
        $card->save();

        return response()->json([
            'success' => true,
            'message' => 'Карточка успешно создана.',
            'card' => $card
        ]);
    }
}
