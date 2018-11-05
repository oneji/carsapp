<?php

namespace App\Http\Controllers\Sto;

use App\Sto;
use App\Defect;
use App\ServicePrice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Validator;

class ServiceController extends Controller
{
    /**
     * 
     */
    public function getPrices($stoSlug)
    {
        $defects = Defect::with('defect_conclusions')->get();
        $prices = ServicePrice::select(
            'service_prices.id as price_id', 
            'service_prices.price', 
            'service_prices.human_hour_price',
            'service_prices.defect_id',
            'service_prices.defect_conclusion_id',
            'defects.defect_name',
            'defect_conclusions.conclusion_name'
        )
        ->join('defect_conclusions', 'defect_conclusions.id', '=', 'service_prices.defect_conclusion_id')
        ->join('defects', 'defects.id', '=', 'service_prices.defect_id')
        ->get();

        // Get all default conclusions
        $defaultDefectConclusions = DB::table('defect_conclusions')->select('*')->where('defect_id', null)->where('deleted', 0)->get();
        // Put default defect conclusions into every defect
        foreach($defects as $defect) {
            foreach($defaultDefectConclusions as $defectConclusion) {
                $collection = collect([
                    'defect_id' => $defect->id,
                    'id' => $defectConclusion->id,
                    'conclusion_name' => $defectConclusion->conclusion_name,
                    'deleted' => $defectConclusion->deleted,
                    'price' => []
                ]);

                $defect->defect_conclusions->push($collection);
            }
        }

        foreach($prices as $price) {
            foreach($defects as $defect) {
                if($defect->id === $price->defect_id) {
                    foreach($defect->defect_conclusions as $conclusion) {
                        if($conclusion['id'] === $price->defect_conclusion_id) {
                            $conclusion['price'] = $price;
                        }
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'defects' => $defects,
            'prices' => $prices
        ]);
    }

    /**
     * 
     */
    public function setPrice($stoSlug, Request $request)
    {
        $price = ServicePrice::find($request->priceId)->update([ 'price' => $request->price ]);
        return response()->json([
            'success' => true,
            'message' => 'Цена успешно установлена.',
            'price' => $price
        ]);
    }

    public function setHumanHourPriceForAll($stoSlug, Request $request)
    {
        DB::table('service_prices')->update([
            'human_hour_price' => $request->human_hour_price
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Общая цена за ч/ч успешно установлена.'
        ]);
    }
}
