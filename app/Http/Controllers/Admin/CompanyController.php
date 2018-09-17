<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\User;
use App\Sto;
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
        $stos = Sto::all();
        return response()->json([
            'companies' => $companies,
            'stos' => $stos
        ]);
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
        $companiesCount = count($request->companies);
        $stosCount = count($request->stos);
        $user = User::find($user_id);

        if($companiesCount === 0 && $stosCount === 0) {
            return response()->json([
                'success' => true,
                'message' => 'Не было выбрано ни одной компании/СТО.'
            ]);
        }

        if($companiesCount > 0) {
            $companies = $request->companies;
            foreach ($companies as $index => $company) {
                if($user->companies->contains($company)) {
                    unset($companies[$index]);
                }
            }

            $user->companies()->attach($companies);
        }

        if($stosCount > 0) {
            $stos = $request->stos;
            foreach ($stos as $index => $sto) {
                if($user->stos->contains($sto)) {
                    unset($stos[$index]);
                }
            }

            $user->stos()->attach($stos);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Пользователь успешно привязан.'
        ]);
    }
}
