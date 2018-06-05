<?php

namespace App\Http\Controllers\Admin;

use App\Sto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoController extends Controller
{
    /**
     * 
     * Get a list of all stos.
     */
    public function get() 
    {
        $stos = Sto::all();
        return response()->json($stos);
    }

    /**
     * Store a newly added sto to database.
     * 
     * 
     */
    public function store(Request $request)
    {
        if($request->hasFile('logo')) {
            $fileFullName = $request->file('logo')->getClientOriginalName(); 
            $filename = pathinfo($fileFullName, PATHINFO_FILENAME);
            $filePath = $request->file('logo')->path();
            $fileExtension = $request->file('logo')->getClientOriginalExtension();
            $fileNameToStore = time().'.'.$fileExtension;
            $path = $request->file('logo')->move(public_path('/uploads/logos/stos'), $fileNameToStore);  
            $fileNameToStore = 'uploads/logos/stos/'.$fileNameToStore;
        } else {
            $fileNameToStore = null;
        }

        $sto = new Sto($request->all());
        $sto->logo = $fileNameToStore;
        $sto->slug = str_slug($request->sto_name);
        $sto->save();

        return response()->json([
            'success' => true,
            'message' => 'СТО успешно создано!',
            'sto' => $sto
        ]);
    }

    /**
     * Bind user to sto.
     * 
     * @param   int $sto_id
     * @param   int $user_id
     * 
     * @return  \Illuminate\Http\Response
     */
    public function bindUser($sto_id, $user_id)
    {
        $company = Sto::find($sto_id)->users()->attach($user_id);
        
        return response()->json([
            'success' => true,
            'message' => 'Пользователь успешно привязан'
        ]);
    }
}
