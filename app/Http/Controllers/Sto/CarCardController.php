<?php

namespace App\Http\Controllers\Sto;

use App\Car;
use App\CarCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CarCardController extends Controller
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
     * Get the whole car card info.
     * 
     * @param   string $sto_slug
     * @param   int $car_id
     * 
     * @return  \Illuminate\Http\Response
     */
    public function getInfo($sto_slug, $car_id)
    {
        // $car = Car::where('id', $car_id)->with('card.defect_options.defect')->first();

        $car = Car::select('cars.id as id', 'year', 'number', 'shape_name', 'brand_name', 'model_name', 'milage', 'vin_code', 'cover_image', 'engine_type_name', 'engine_capacity')
                        ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                        ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                        ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                        ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                        ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
                        ->with('card.defect_options.defect', 'attachments')
                        ->where('cars.id', $car_id)->first(); 
        
        return response()->json($car);
    }
}
