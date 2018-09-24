<?php

namespace App\Http\Controllers\Sto;

use App\Car;
use App\CarCard;
use App\Company;
use App\CarAttachment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class CarController extends Controller
{
    /**
     * Store a newly created car card to a database.
     * 
     * @param   string $sto_slug
     * @param   int $car_id
     * 
     * @return  \Illuminate\Http\Response
     */
    public function createCard($sto_slug, $car_id)
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

    /**
     * Store a newly created car to the database.
     * 
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\Reponse
     */
    public function store(Request $request, $sto_slug) 
    {   
        $company = Company::find($request->company_id);

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
        $car->has_gps = $request->has_gps === 'true' ? 1 : 0;
        
        if($request->teh_osmotr_end_date !== null && $request->teh_osmotr_end_date !== 'null')
        $car->teh_osmotr_end_date = Carbon::parse($request->teh_osmotr_end_date);

        if($request->tint_end_date !== null && $request->tint_end_date !== 'null')
            $car->tint_end_date = Carbon::parse($request->tint_end_date);

        if($request->milage !== null && $request->milage !== 'null') 
            $car->milage = $request->milage;

        if($request->engine_capacity !== null && $request->engine_capacity !== 'null')
            $car->engine_capacity = str_replace(',', '.', $request->engine_capacity);

        $car->save();       

        if($request->driver_id !== null && $request->driver_id !== 'null') {
            $car->drivers()->attach([
                $request->driver_id => [
                    'active' => 1,
                    'start_date' => Carbon::now()
                ]
            ]);
        }
        
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

    public function addAttachments(Request $request, $sto_slug, $car_id)
    {
        $car = Car::find($car_id);

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
            'message' => 'Файлы успешно сохранены.',
            'files' => $carAttachments
        ]);
    }
}
