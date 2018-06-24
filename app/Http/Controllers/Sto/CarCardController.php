<?php

namespace App\Http\Controllers\Sto;

use App\Car;
use App\CarCard;
use App\CarCardComment;
use App\DefectType;
use App\Sto;
use JWTAuth;
use App\EquipmentType;
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
        $sto = Sto::where('slug', $sto_slug)->first();
        $car = Car::select('cars.id as id', 'year', 'number', 'shape_name', 'brand_name', 'model_name', 'milage', 'vin_code', 'cover_image', 'engine_type_name', 'engine_capacity', 'transmission_name')
                        ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                        ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                        ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                        ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                        ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
                        ->with('attachments', 'card.comments.user', 'card.defect_acts.defect_options.defect', 'card.defect_acts.equipment')
                        ->where('cars.id', $car_id)->with('drivers')->first(); 
        $defect_info = DefectType::where('sto_id', $sto->id)->with('defects.defect_options')->get();
        $equipment = EquipmentType::all();

        return response()->json([
            'car' => $car,
            'defects_info' => $defect_info,
            'equipment' => $equipment
        ]);
    }

    /**
     * Save a newly added defects.
     * 
     * @param   string $sto_slug
     * @param   int $card_id
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function saveDefects(Request $request, $sto_slug, $card_id)
    {
        $card = CarCard::where('id', $card_id)->first();
        $defectOptions = $request->only('defect_options');
        
        foreach($defectOptions as $option)
            $card->defect_options()->sync($option);

        return response()->json([
            'success' => true,
            'message' => 'Дефектный акт сохранен.'
        ]);
    }

    /**
     * Store a newly created comment in the database.
     * 
     * @param   string $sto_slug
     * @param   int $card_id
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function storeComment(Request $request, $sto_slug, $card_id)
    {
        $card = CarCard::where('id', $card_id)->first();
        $user = JWTAuth::parseToken()->authenticate();
        $comment = new CarCardComment();
        $comment->comment = $request->comment;
        $comment->car_card_id = $card->id;
        $comment->user_id = $user->id;  
        $comment->save();     
        $comment->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Комментарий успешно добавлен.',
            'comment' => $comment,
        ]);
    }
}
