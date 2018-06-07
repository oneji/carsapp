<?php

namespace App\Http\Controllers\Sto;

use App\Sto;
use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    /**
     * Get all sto companies.
     * 
     * @param   string $sto_slug
     * 
     * @return  \Illuminate\Http\Response
     */
    public function get($sto_slug)
    {
        $sto = Sto::where('slug', $sto_slug)->with('companies.cars')->first();

        return response()->json($sto);
    }

    /**
     * Get all company cars.
     * 
     * @param   string $sto_slug
     * @param   int $company_id
     * 
     * @return  \Illuminate\Http\Response
     */
    public function getCars($sto_slug, $company_id) 
    {
        $company = Company::where('id', $company_id)->with('cars.drivers')->first();

        return response()->json($company);
    }
}
