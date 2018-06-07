<?php

namespace App\Http\Controllers\Sto;

use App\Car;
use App\CarCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CarController extends Controller
{
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
