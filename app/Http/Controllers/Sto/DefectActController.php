<?php

namespace App\Http\Controllers\Sto;

use App\CardCard;
use App\DefectAct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class DefectActController extends Controller
{
    /**
     * Get all defect acts.
     */
    public function get($sto_slug, $card_id)
    {
        $defectActs = DefectAct::where('car_card_id', $card_id)->get();
        return response()->json($defectActs);
    }
    /**
     * Store a newly created defect act.
     * 
     * @param   string $sto_slug
     * @param   int $card_id
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function store(Request $request, $sto_slug, $card_id)
    {
        $defectOptions = $request->only('defect_options');
        $equipment = $request->only('equipment');
        $defectAct = new DefectAct();
        $defectAct->car_card_id = $card_id;
        $defectAct->defect_act_date = Carbon::now();
        $defectAct->save();

        foreach($defectOptions as $option)
            $defectAct->defect_options()->attach($option);

        foreach($equipment as $eq) 
            $defectAct->equipment()->attach($eq);

        return response()->json([
            'success' => true,
            'message' => 'Дефектный акт успешно создан.',
            'act' => $defectAct
        ]);
    }
}
