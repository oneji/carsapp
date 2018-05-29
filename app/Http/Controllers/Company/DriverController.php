<?php

namespace App\Http\Controllers\Company;

use App\Driver;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DriverController extends Controller
{
    /**
     * Store a newly created driver to the database.
     * 
     */
    public function store(Request $request) 
    {      
        if($request->hasFile('attachments')) {            
            foreach($request->attachments as $file) {
                $fileFullName = $file->getClientOriginalName(); 
                $filename = pathinfo($fileFullName, PATHINFO_FILENAME);
                $filePath = $file->path();
                $fileExtension = $file->getClientOriginalExtension();
                $fileNameToStore = uniqid().'.'.$fileExtension;
                $path = $file->move(public_path('/uploads/attachments/cars'), $fileNameToStore);  
                $fileNameToStore = 'uploads/attachments/cars'.$fileNameToStore;
            }
        } else {
            $fileNameToStore = null;
        }

        return response()->json([
            'success' => true,
            'message' => 'Водитель успешно создан.'
        ]);
    }
}
