<?php

namespace App\Http\Controllers\Company;

use App\RepairRequest;
use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RepairRequestController extends Controller
{
    /**
     * Store a newly created request to a database.
     */
    public function store(Request $request, $company_slug)
    {
        $company = Company::where('slug', $company_slug)->first();
        $req = new RepairRequest($request->all());
        $req->company_id = $company->id;
        $req->comment = $request->comment;
        $req->save();

        return response()->json([
            'success' => true,
            'message' => 'Заявка успешно отправлена.',
            'request' => $req
        ]);
    }

    /**
     * Get all requests.
     * 
     * @param  string $company_slug
     */
    public function get($company_slug) 
    {
        $company = Company::where('slug', $company_slug)->first();
        $requests = RepairRequest::select('repair_requests.*', 'company_name', 'sto_name')
                                ->join('companies', 'companies.id', '=', 'repair_requests.company_id')
                                ->join('stos', 'stos.id', '=', 'repair_requests.sto_id')
                                ->where('repair_requests.company_id', $company->id)
                                ->with([
                                    'car' => function($query) {
                                        $query->select('cars.*', 'shape_name', 'brand_name', 'model_name', 'transmission_name', 'engine_type_name')
                                                ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                                                ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                                                ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                                                ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                                                ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')    
                                                ->where('reserved', 0)  
                                                ->where('sold', 0)                    
                                                ->get();
                                    }
                                ])->get();

        return response()->json($requests);
    }
}
