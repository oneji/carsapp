<?php

namespace App\Http\Controllers\Company;

use App\Company;
use App\StoRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoRequestController extends Controller
{
    /**
     * Get all sto requests.
     * 
     * @param   string $company_slug
     * 
     * @return  \Illuminate\Http\Response
     */
    public function get($company_slug) 
    {
        $company = Company::where('slug', $company_slug)->first();
        $requests = StoRequest::select('sto_requests.id', 'company_name', 'sto_name', 'status', 'sto_requests.created_at')
                                ->join('companies', 'companies.id', '=', 'sto_requests.company_id')
                                ->join('stos', 'stos.id', '=', 'sto_requests.sto_id')
                                ->where('sto_requests.company_id', $company->id)->get();

        return response()->json($requests);
    }

    /**
     * Store a newly created sto request to database.
     * 
     * @param   string $company_slug
     * @param   int $sto_id
     * 
     * @return  \Illuminate\Http\Response
     */
    public function store($company_slug, $sto_id) 
    {
        $company = Company::where('slug', $company_slug)->first();
        $req = new StoRequest();
        $req->company_id = $company->id;
        $req->sto_id = $sto_id;
        $req->save();

        return response()->json([
            'success' => true,
            'message' => 'Заявка СТО успешно отправлена.',
            'request' => $req
        ]);
    }

    /**
     * Cancel sent request.
     * 
     * @param   int $id
     */
    public function cancel($company_slug, $id) 
    {
        $request = StoRequest::find($id)->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
