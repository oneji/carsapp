<?php

namespace App\Http\Controllers\Sto;

use App\Sto;
use App\StoRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoRequestController extends Controller
{
    /**
     * Get all company requests.
     * 
     * @param   string $sto_slug
     * 
     * @return  \Illuminate\Http\Response
     */
    public function get($sto_slug) 
    {
        $sto = Sto::where('slug', $sto_slug)->first();
        $requests = StoRequest::select('sto_requests.id', 'company_name', 'sto_name', 'status', 'sto_requests.created_at')
                                ->join('companies', 'companies.id', '=', 'sto_requests.company_id')
                                ->join('stos', 'stos.id', '=', 'sto_requests.sto_id')
                                ->where('sto_requests.sto_id', $sto->id)
                                ->where('status', 0)->get();

        return response()->json($requests);
    }

    /**
     * Accept company request.
     * 
     * @param   string $sto_slug
     * @param   int $id
     * 
     * @return  \Illuminate\Http\Response
     */
    public function accept(Request $request, $sto_slug, $id)
    {
        $status = 0;
        $message = '';
        if($request->type === 1) {
            $status = 1;
            $message = 'Заявка успешно принята. Можно начинать сотрудничество.';
        } 
        
        if($request->type === 0) {
            $status = 2;
            $message = 'Заявка отклонена.';
        }

        $req = StoRequest::find($id);
        $req->status = $status;
        $req->save();

        $sto = Sto::where('slug', $sto_slug)->first();
        $sto->companies()->attach($req->company_id);

        return response()->json([
            'success' => true,
            'message' => $message,
            'sto' => $sto
        ]);
    }
}
