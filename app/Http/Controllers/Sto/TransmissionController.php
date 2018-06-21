<?php

namespace App\Http\Controllers\Sto;

use App\Transmission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransmissionController extends Controller
{
    /**
     * Get a list of tranmissions.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        $transmissions = Transmission::all();
        return response()->json($transmissions);
    }

    /**
     * Update a transmission.
     * 
     * @param   \Illuminate\Http\Request $request
     * @param   string $sto_slug
     * @param   int $id
     */
    public function update(Request $request, $sto_slug, $id) 
    {
        $transmission = Transmission::find($id);
        $transmission->transmission_name = $request->transmission_name;
        $transmission->save();

        return response()->json([
            'success' => true,
            'message' => 'Трансмиссия успешно изменена.',
            'type' => $transmission
        ]);
    }

    /**
     * Store a newly created transmission in the database.
     * 
     * @param   \Illuminate\Http\Request $request
     * @param   string $sto_slug
     * 
     * @return  Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $sto_slug)
    {
        $transmission = new Transmission();
        $transmission->transmission_name = $request->transmission_name;
        $transmission->save();

        return response()->json([
            'success' => true,
            'message' => 'Трансмиссия успешно добавлена.',
            'transmission' => $transmission
        ]);
    }
}
