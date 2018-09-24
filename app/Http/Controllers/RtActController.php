<?php

namespace App\Http\Controllers;

use App\RtAct;
use App\Sto;
use App\Car;
use Illuminate\Http\Request;

class RtActController extends Controller
{
    /**
     * Store a newly created RT act in the db.
     */
    public function store(Request $request)
    {
        $act = new RtAct($request->all());
        $act->save();

        $values = json_decode($request->values);
        for($i = 0; $i < count($values); $i++) {
            if($values[$i] !== null) {
                $act->checklist_items()->attach($values[$i]->id, [
                    'status' => $values[$i]->status,
                    'comment' => $values[$i]->comment
                ]);
            }
        }
            

        return response()->json([
            'success' => true,
            'message' => 'Акт приема передачи успешно создан.'
        ]);
    }

    /**
     * 
     */
    public function getFullInfo(Request $request)
    {
        $sto = Sto::where('slug', $request->sto_slug)->with('companies')->first();
        $car = Car::select('cars.*', 'shape_name', 'brand_name', 'model_name', 'engine_type_name', 'transmission_name')
                        ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                        ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                        ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                        ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                        ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
                        ->with('attachments', 'card.comments.user', 'card.defect_acts.defect_options.defect', 'card.defect_acts.equipment')
                        ->where('sold', 0)
                        ->where('cars.id', $request->car_id)->with([ 'drivers' => function($q) {
                            $q->where('active', 1)->get();
                        }])->first(); 
        $companies = $sto->companies;

        return response()->json([
            'car' => $car,
            'companies' => $companies
        ]);
    }
}
