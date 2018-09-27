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
        $company = Company::where('id', $company_id)->with([
            'cars' => function($query) {                
                $query->with('drivers')
                    ->select('cars.*', 'shape_name', 'brand_name', 'model_name', 'engine_type_name', 'transmission_name')
                    ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                    ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                    ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                    ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                    ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
                    ->with(['drivers' => function($q) {
                        $q->where('active', 1)->get();
                    }])
                    ->where('sold', 0)
                    ->get(); 
            }, 
            'cars.card'
        ])->first();

        return response()->json($company);
    }
}
