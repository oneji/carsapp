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
        $requests = RepairRequest::select('repair_requests.*', 'company_name', 'sto_name')
                                ->join('companies', 'companies.id', '=', 'repair_requests.company_id')
                                ->join('stos', 'stos.id', '=', 'repair_requests.sto_id')
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
            'message' => 'Автомобиль успешно добавлен в очередь на привоз.',
            'request' => $req
        ]);
    }

    /**
     * Indicate that car was brought to STO.
     * 
     * @param   string $sto_slug
     * @param   int $request_id
     * 
     * @return  \Illuminate\Http\JsonResponse 
     */
    public function toSto($sto_slug, $request_id) 
    {
        $req = RepairRequest::find($request_id);
        $req->status = 2;
        $req->save();

        return response()->json([
            'success' => true,
            'message' => 'Автомобиль был успешно доставлен в СТО.'
        ]);
    }

    /**
     * 
     */
    public function repairDone(Request $request, $sto_slug, $request_id) 
    {
        $req = RepairRequest::find($request_id);
        $req->status = 3;
        $req->repair_date = Carbon::parse($request->repair_date);
        $req->save();

        return response()->json([
            'success' => true,
            'message' => 'Ремонт автомобиля успешно завершен.',
            'request' => $req
        ]);
    } 
}
