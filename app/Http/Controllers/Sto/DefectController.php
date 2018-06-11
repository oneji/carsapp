<?php

namespace App\Http\Controllers\Sto;

use App\Sto;
use App\DefectOption;
use App\DefectType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DefectController extends Controller
{
    /**
     * Get all defect options.
     * 
     * @param   string $sto_slug
     * 
     * @return  \Illuminate\Http\Response
     */
    public function getOptions($sto_slug)
    {
        $sto = Sto::where('slug', $sto_slug)->first();
        $options = DefectOption::where('sto_id', $sto->id)->get();

        return response()->json($options);
    }

    /**
     * Get all defect types.
     * 
     * @param   string $sto_slug
     * 
     * @return  \Illuminate\Http\Response
     */
    public function getTypes($sto_slug)
    {
        $sto = Sto::where('slug', $sto_slug)->first();
        $types = DefectType::all();

        return response()->json($types);
    }

    /**
     * Get all defect types.
     * 
     * @param   string $sto_slug
     * 
     * @return  \Illuminate\Http\Response
     */
    public function getFullInfo($sto_slug)
    {
        $sto = Sto::where('slug', $sto_slug)->first();
        $defect_info = DefectType::where('sto_id', $sto->id)->with('defects.defect_options')->get();

        return response()->json([
            'success' => true,
            'defect_info' => $defect_info
        ]);
    }
}
