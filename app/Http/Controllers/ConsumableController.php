<?php

namespace App\Http\Controllers;

use App\Consumable;
use App\CarCard;
use App\Car;
use Illuminate\Http\Request;

class ConsumableController extends Controller
{
    /**
     * Get all consumables.
     * 
     * @param   int $car_id
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function get($car_id)
    {
        $card = CarCard::where('car_id', $car_id)->with([
            'consumables' => function($query) {
                $query->select('consumables.*')->get();
            }
        ])->first();
        $consumables = Consumable::where('car_card_id', $card->id)->orWhere('car_card_id', null)->with('car_card.car')->get();
        return response()->json([
            'consumables' => $consumables,
            'data' => $card
        ]);
    }

    /**
     * Store a newly changed consumable.
     * 
     * @param   \Illuminate\Http\Request $request
     * @param   int $car_id
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $car_id) 
    {
        $card = CarCard::where('car_id', $car_id)->first();

        $additionalData = [
            'car_card_id' => $card->id,
            'consumable_id' => $request->consumable_id
        ];

        $card->load([
            'consumables' => function($query) use ($additionalData) {
                $query->where('car_card_consumable.car_card_id', $additionalData['car_card_id'])
                    ->where('car_card_consumable.consumable_id', $additionalData['consumable_id'])
                    ->get();
            }
        ]);

        if(count($card->consumables) > 0) {
            $card->consumables()->updateExistingPivot($request->consumable_id, [
                'change_date' => $request->change_date,
                'change_date_milage' => $request->change_date_milage,
                'recommended_change_milage' => $request->recommended_change_milage
            ]);
        } else {
            $card->consumables()->attach($request->consumable_id, [
                'change_date' => $request->change_date,
                'change_date_milage' => $request->change_date_milage,
                'recommended_change_milage' => $request->recommended_change_milage
            ]);
        }        

        return response()->json([
            'success' => true,
            'message' => 'Расходный материал успешно сохранен.'
        ]);
    }

    public function store(Request $request, $car_id) 
    {
        $card = CarCard::where('car_id', $car_id)->first();
        $consumable = new Consumable();
        $consumable->consumable_name = $request->consumable_name;
        $consumable->car_card_id = $card->id;
        $consumable->icon = '/static/icons/check-mark.svg';
        $consumable->save();

        return response()->json([
            'success' => true,
            'message' => 'Расходный материал успешно создан.', 
            'consumable' => $consumable
        ]);
    }
}
