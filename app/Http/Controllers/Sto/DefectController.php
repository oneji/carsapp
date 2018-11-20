<?php

namespace App\Http\Controllers\Sto;

use App\Sto;
use App\DefectOption;
use App\DefectType;
use App\DefectConclusion;
use App\ServicePrice;
use App\Defect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $options = DefectOption::where('deleted', 0)->get();
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
        $types = DefectType::where('deleted', 0)->get();
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
        $defect_info = DefectType::with([
            'defects' => function($query) {
                $query->with([
                    'defect_conclusions' => function($query) {
                        $query->where('deleted', 0)->get();
                    }, 
                    'defect_options' => function($query) {
                        $query->where('deleted', 0)->get();
                    }
                ])->where('deleted', 0)->get();
            }
        ])->where('deleted', 0)->get();

        $defaultDefectConditions = DB::table('defect_options')->select('*')->where([
            'defect_id' => null,
            'deleted' => 0
        ])->get();
        $defaultDefectConclusion = DB::table('defect_conclusions')->select('*')->where([
            'defect_id' => null,
            'deleted' => 0
        ])->get();
        // Put default defect options/conslusions into every defect
        foreach($defect_info as $defectType) {
            foreach($defectType->defects as $defect) {
                foreach($defaultDefectConditions as $defectOption) {
                    $defect->defect_options->push($defectOption);
                }

                foreach($defaultDefectConclusion as $defectConslusion) {
                    $defect->defect_conclusions->push($defectConslusion);
                }
            }
        }

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
        $type = new DefectType();
        $type->defect_type_name = $request->defect_type_name;
        $type->save();

        return response()->json([
            'success' => true,
            'message' => 'Тип дефекта успешно создан.',
            'type' => $type
        ]);
    }
    
    public function getAll($sto_slug)
    {
        $allDefects = DefectType::with([
            'defects' => function($query) {
                $query->with([
                    'defect_conclusions' => function($query) {
                        $query->where('deleted', 0)->get();
                    }, 
                    'defect_options' => function($query) {
                        $query->where('deleted', 0)->get();
                    }
                ])->where('deleted', 0)->get();
            }
        ])->where('deleted', 0)->get();

        $defaultDefectConditions = DB::table('defect_options')->select('*')->where('defect_id', null)->where('deleted', 0)->get();
        $defaultDefectConclusion = DB::table('defect_conclusions')->select('*')->where('defect_id', null)->where('deleted', 0)->get();
        // Put default defect options into every defect
        foreach($allDefects as $defectType) {
            foreach($defectType->defects as $defect) {
                foreach($defaultDefectConditions as $defectOption) {
                    $defect->defect_options->push($defectOption);
                }

                foreach($defaultDefectConclusion as $defectConslusion) {
                    $defect->defect_conclusions->push($defectConslusion);
                }
            }
        }

        return response()->json([
            'success' => true,
            'allDefects' => $allDefects
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
        // Check if defect already exists
        $oldDefect = Defect::where([
            'defect_name' => $request->defect_name,
            'deleted' => 0
        ])->get();
        
        if(count($oldDefect)) {
            return response()->json([
                'success' => false,
                'message' => 'Деталь с таким именем уже существует.'
            ]);
        } else {
            $defect = new Defect();
            $defect->defect_name = $request->defect_name;
            $defect->defect_type_id = $request->defect_type_id;
            $defect->save();
    
            // Get all default defect conclusions
            $defaultDefectConclusions = DB::table('defect_conclusions')->select('*')->where('defect_id', null)->get();
            // Save default price for each defect conclusion
            $defaultPrices = [];
            foreach($defaultDefectConclusions as $conclusion) {
                array_push($defaultPrices, new ServicePrice([
                    'defect_id' => $defect->id,
                    'defect_conclusion_id' => $conclusion->id,
                ]));
            }
            // Save prices
            $defect->service_prices()->saveMany($defaultPrices);
    
            return response()->json([
                'success' => true,
                'message' => 'Деталь успешно создана.',
                'defect' => $defect
            ]);
        }
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
        $defectOption = DefectOption::find($id);
        $defectOption->defect_option_name = $request->defect_option_name;
        $defectOption->defect_id = $defectOption->defect_id !== null ? $request->defect_id : null;
        $defectOption->save();

        return response()->json([
            'success' => true,
            'message' => 'Вид дефекта успешно изменен.'
        ]);
    }

    /**
     * Get all defect conslusions.
     */
    public function getConclusions()
    {
        $conclusions = DefectConclusion::select('defect_conclusions.id as id', 'conclusion_name', 'defect_name')
            ->join('defects', 'defect_conclusions.defect_id', '=', 'defects.id')
            ->where('deleted', 0)
            ->get();

        return response()->json($conclusions);
    }
    
    /**
     * 
     */
    public function storeConclusion(Request $request)
    {
        $conclusion = new DefectConclusion();
        $conclusion->conclusion_name = $request->conclusion_name;
        $conclusion->defect_id = $request->defect_id;
        $conclusion->save();

        return response()->json([
            'success' => true,
            'message' => 'Заключение успешно создано.',
            'conclusion' => $conclusion
        ]);
    }

    /**
     * 
     */
    public function updateConclusion(Request $request, $sto_slug, $id)
    {
        $conclusion = DefectConclusion::find($id);
        $conclusion->conclusion_name = $request->conclusion_name;
        $conclusion->defect_id = $conclusion->defect_id !== null ? $request->defect_id : null;
        $conclusion->save();

        return response()->json([
            'success' => true,
            'message' => 'Заключение успешно изменено.'
        ]);
    }

    /**
     * 
     */
    public function deleteType($sto_slug, $id)
    {
        $defectType = DefectType::where('id', $id)->update([ 'deleted' => 1 ]);        
        return response()->json([
           'success' => true,
           'message' => 'Чек лист успешно удален.' 
        ]);
    }
    
    /**
     * 
     */
    public function deleteDefect($sto_slug, $id)
    {
        $defect = Defect::where('id', $id)->update([ 'deleted' => 1 ]);
        return response()->json([
            'success' => true,
            'message' => 'Деталь успешно удалена.' 
        ]);
    }
    
    /**
     * 
     */
    public function deleteOption($sto_slug, $id)
    {
        $option = DefectOption::where('id', $id)->update([ 'deleted' => 1 ]);
        return response()->json([
            'success' => true,
            'message' => 'Состояние успешно удалено.' 
        ]);
    }
    
    /**
     * 
     */
    public function deleteConclusion($sto_slug, $id)
    {
        $conclusion = DefectConclusion::where('id', $id)->update([ 'deleted' => 1 ]);
        return response()->json([
            'success' => true,
            'message' => 'Заключение успешно удалено.' 
        ]);
    }

    /**
     * 
     */
    public function fillServicePrices()
    {
        $defects = Defect::all();
        $defaultDefectConclusions = DB::table('defect_conclusions')->select('*')->where('defect_id', null)->get();

        foreach($defects as $defect) {
            $defaultPrices = [];
            foreach($defaultDefectConclusions as $conclusion) {
                array_push($defaultPrices, new ServicePrice([
                    'defect_id' => $defect->id,
                    'defect_conclusion_id' => $conclusion->id
                ]));
            }

            foreach($defect->defect_conclusions as $conclusion) {
                array_push($defaultPrices, new ServicePrice([
                    'defect_id' => $defect->id,
                    'defect_conclusion_id' => $conclusion->id
                ]));
            }

            $defect->service_prices()->saveMany($defaultPrices);
        }

        return response()->json([ 'success' => true ]);
    }
}
