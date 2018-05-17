<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    /**
     * 
     * Get a list of all companies.
     */
    public function get() 
    {
        $companies = Company::all();
        return response()->json($companies);
    }

    /**
     * Store a newly added company to database.
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
            $path = $request->file('logo')->move(public_path('/uploads/logos/companies'), $fileNameToStore);  
        } else {
            $fileNameToStore = null;
        }

        $company = new Company($request->all());
        $company->logo = 'uploads/logos/companies/'.$fileNameToStore;
        $company->slug = str_slug($request->company_name);
        $company->save();

        return response()->json([
            'success' => true,
            'message' => 'Компания успешно создана!',
            'company' => $company
        ]);
    }
}
