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

        if($request->hasFile('defect_act_img')) {
            $fileExtension = $request->file('defect_act_img')->getClientOriginalExtension();
            $fileNameToStore = uniqid().'.'.$fileExtension;
            $path = $request->file('defect_act_img')->move(public_path('/uploads/attachments/defect_acts'), $fileNameToStore);  
            $fileNameToStore = 'uploads/attachments/defect_acts/'.$fileNameToStore;
        } else {
            $fileNameToStore = null;
        }

        $defectAct = new DefectAct($request->all());
        $defectAct->car_card_id = $card_id;
        $defectAct->defect_act_date = Carbon::now();
        $defectAct->defect_act_img = $fileNameToStore;
        $defectAct->save();

        foreach($defectOptions as $option)
            $defectAct->defect_options()->attach($option);

        foreach($equipment as $eq) 
            $defectAct->equipment()->attach($eq);

        $defectAct->load(['defect_options.defect', 'equipment']);

        return response()->json([
            'success' => true,
            'message' => 'Дефектный акт успешно создан.',
            'act' => $defectAct
        ]);
    }
}
