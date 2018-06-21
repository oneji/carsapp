<?php

namespace App\Http\Controllers\Sto;

use App\EngineType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EngineTypeController extends Controller
{
    /**
     * Get a list of engine types.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        $engineTypes = EngineType::all();
        return response()->json($engineTypes);
    }

    /**
     * Update an engine type.
     * 
     * @param   \Illuminate\Http\Request $request
     * @param   string $sto_slug
     * @param   int $id
     */
    public function update(Request $request, $sto_slug, $id) 
    {
        $engineType = EngineType::find($id);
        $engineType->engine_type_name = $request->engine_type_name;
        $engineType->save();

        return response()->json([
            'success' => true,
            'message' => 'Тип ДВС успешно изменен.',
            'type' => $engineType
        ]);
    }

    /**
     * Store a newly created engine type in the database.
     * 
     * @param   \Illuminate\Http\Request $request
     * @param   string $sto_slug
     * 
     * @return  Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $sto_slug)
    {
        $engineType = new EngineType();
        $engineType->engine_type_name = $request->engine_type_name;
        $engineType->save();

        return response()->json([
            'success' => true,
            'message' => 'ДВС успешно добавлен.',
            'type' => $engineType
        ]);
    }
}
