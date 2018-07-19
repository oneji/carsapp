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
        $requests = RepairRequest::select('repair_requests.id', 'company_name', 'sto_name', 'status', 'comment', 'repair_requests.created_at', 'receive_date')
                                ->join('companies', 'companies.id', '=', 'repair_requests.company_id')
                                ->join('stos', 'stos.id', '=', 'repair_requests.sto_id')
                                ->where('repair_requests.company_id', $company->id)->get();

        return response()->json($requests);
    }
}
