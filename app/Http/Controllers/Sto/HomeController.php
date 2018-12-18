<?php

namespace App\Http\Controllers\Sto;

use App\RepairRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * 
     */
    public function getStatistics()
    {
        $repairRequestsCount = count(RepairRequest::where('status', 3)->get());
        // Get number of cars which are repaired
        return response()->json([
            'repairedCarsCount' => $repairRequestsCount,
        ]);
    }
}
