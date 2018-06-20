<?php

namespace App\Http\Controllers\Sto;

use App\Sto;
use App\DefectOption;
use App\DefectType;
use App\Defect;
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

    /**
     * Store a newly created defect option in a database.
     * 
     * @param   string $sto_slug
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function storeOption(Request $request, $sto_slug)
    {
        $sto = Sto::where('slug', $sto_slug)->first();
        $option = new DefectOption();
        $option->defect_option_name = $request->defect_option_name;
        $option->defect_id = $request->defect_id;
        $option->save();

        return response()->json([
            'success' => true,
            'message' => 'Вид дефекта успешно создан.',
            'option' => $option
        ]);
    }

    /**
     * Store a newly created defect type in a database.
     * 
     * @param   string $sto_slug
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function storeType(Request $request, $sto_slug)
    {
        $sto = Sto::where('slug', $sto_slug)->first();
        $type = new DefectType();
        $type->defect_type_name = $request->defect_type_name;
        $type->sto_id = $sto->id;
        $type->save();

        return response()->json([
            'success' => true,
            'message' => 'Тип дефекта успешно создан.',
            'type' => $type
        ]);
    }
    
    public function getAll($sto_slug)
    {
        $sto = Sto::where('slug', $sto_slug)->first(); 

        $allDefects = DefectType::where('sto_id', $sto->id)->with([
            'defects' => function($query) {
                $query->with('defect_options')->get();
            }
        ])->get();

        return response()->json([
            'success' => true,
            'allDefects' => $allDefects,
        ]);
    }

    /**
     * Store a newly created defect in a database.
     * 
     * @param   string $sto_slug
     * @param   \Illuminate\Http\Request $request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function storeDefect(Request $request, $sto_slug)
    {
        $sto = Sto::where('slug', $sto_slug)->first();
        $defect = new Defect();
        $defect->defect_name = $request->defect_name;
        $defect->defect_type_id = $request->defect_type_id;
        $defect->save();

        return response()->json([
            'success' => true,
            'message' => 'Дефект успешно создан.',
            'defect' => $defect
        ]);
    }

    /**
     * Update defect type.
     * 
     * @param   string $sto_slug
     * @param   \Illuminate\Http\Request $request
     * @param   int $id
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function updateType(Request $request, $sto_slug, $id) 
    {
        $sto = Sto::where('slug', $sto_slug)->first();
        $defectType = DefectType::find($id);
        $defectType->defect_type_name = $request->defect_type_name;
        $defectType->save();

        return response()->json([
            'success' => true,
            'message' => 'Тип дефекта успешно изменен.'
        ]);
    }

    /**
     * Update defect.
     * @param   string $sto_slug
     * @param   \Illuminate\Http\Request $request
     * @param   int $id
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function updateDefect(Request $request, $sto_slug, $id) 
    {
        $sto = Sto::where('slug', $sto_slug)->first();
        $defect = Defect::find($id);
        $defect->defect_name = $request->defect_name;
        $defect->defect_type_id = $request->defect_type_id;
        $defect->save();

        return response()->json([
            'success' => true,
            'message' => 'Дефект успешно изменен.'
        ]);
    }

    /**
     * Update defect option.
     * @param   string $sto_slug
     * @param   \Illuminate\Http\Request $request
     * @param   int $id
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function updateOption(Request $request, $sto_slug, $id) 
    {
        $sto = Sto::where('slug', $sto_slug)->first();
        $defectOption = DefectOption::find($id);
        $defectOption->defect_option_name = $request->defect_option_name;
        $defectOption->defect_id = $request->defect_id;
        $defectOption->save();

        return response()->json([
            'success' => true,
            'message' => 'Вид дефекта успешно изменен.'
        ]);
    }
}
