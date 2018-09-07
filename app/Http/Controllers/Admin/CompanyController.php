<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    /** 
     * Get a list of all companies.
     * 
     * @return  \Illuminate\Http\Response
     */
    public function get() 
    {
        $companies = Company::all();
        return response()->json($companies);
    }

    /**
     * Store a newly added company to database.
     * 
     * @param   \Illuminate\Http\Request
     * 
     * @return  \Illuminate\Http\Response
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
            $fileNameToStore = 'uploads/logos/companies/'.$fileNameToStore;
        } else {
            $fileNameToStore = null;
        }

        $company = new Company($request->all());
        $company->logo = $fileNameToStore;
        $company->slug = str_slug($request->company_name);
        $company->save();

        return response()->json([
            'success' => true,
            'message' => 'Компания успешно создана.',
            'company' => $company
        ]);
    }

    /**
     * Bind user to a company.
     * 
     * @param   int $company_id
     * @param   int $user_id
     * 
     * @return  \Illuminate\Http\Response
     */
    public function bindUser(Request $request, $user_id)
    {
        $user = User::find($user_id)->companies()->attach($request->companies);
        
        return response()->json([
            'success' => true,
            'message' => 'Пользователь успешно привязан.'
        ]);
    }
}
