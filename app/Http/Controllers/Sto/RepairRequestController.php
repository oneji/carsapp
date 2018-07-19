<?php

namespace App\Http\Controllers\Sto;

use App\Sto;
use App\RepairRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class RepairRequestController extends Controller
{
    /**
     * Get all requests.
     * 
     * @param   string $company_slug
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function get($sto_slug) 
    {
        $sto = Sto::where('slug', $sto_slug)->first();
        $requests = RepairRequest::select('repair_requests.id', 'company_name', 'sto_name', 'status', 'comment', 'repair_requests.created_at', 'receive_date')
                                ->join('companies', 'companies.id', '=', 'repair_requests.company_id')
                                ->join('stos', 'stos.id', '=', 'repair_requests.sto_id')->get();

        return response()->json($requests);
    }

    /**
     * Add car to repair queue.
     * 
     * @param   int $request_id
     * 
     * @return  \Illuminate\Http\JsonResponse
     */
    public function toQueue(Request $request, $sto_slug, $request_id)
    {
        $req = RepairRequest::find($request_id);
        $req->status = 1;
        $req->receive_date = Carbon::parse($request->receive_date);
        $req->save();

        return response()->json([
            'success' => true,
            'message' => 'Автомобиль успешно добавлен в очередь на привоз.'
        ]);
    }
}
