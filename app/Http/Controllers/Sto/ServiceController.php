<?php

namespace App\Http\Controllers\Sto;

use App\Sto;
use App\ServiceCategory;
use App\ServiceType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class ServiceController extends Controller
{
    /**
     * Store a newly created service category to database.
     * 
     * @param   string $sto_slug
     * @param   \Illuminate\Http\Request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function storeCategory(Request $request, $sto_slug)
    {
        $validator = Validator::make($request->all(), [
            'service_category_name' => 'required'
        ]);

        if($validator->fails()) {
            $errors = $validator->messages()->toJson();
            return response()->json([
                'success' => false,
                'message' => $errors
            ]);
        }

        $sto = Sto::where('slug', $sto_slug)->first();
        $category = new ServiceCategory($request->only('service_category_name'));
        $category->sto_id = $sto->id;
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Категория успешно создана.',
            'category' => $category
        ]);
    }

    /**
     * Get all service categories from database for a specific STO.
     * 
     * @param   string $sto_slug
     * 
     * @return  \Illuminate\Http\Response
     */
    public function getCategories($sto_slug)
    {
        $sto = Sto::where('slug', $sto_slug)->first();
        $categories = ServiceCategory::where('sto_id', $sto->id)->get();

        return response()->json($categories);
    }


    public function updateServiceCategory(Request $request, $sto_slug, $id) 
    {
        $category = ServiceCategory::where('id', $id)->update([
            'service_category_name' => $request->service_category_name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Категория успешно изменена.',
            'category' => $category
        ]);
    }

    /**
     * Store a newly created service to a database.
     * @param   string $sto_slug
     * @param   \Illuminate\Http\Request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function storeService(Request $request, $sto_slug)
    {
        $sto = Sto::where('slug', $sto_slug)->first();
        $validator = Validator::make($request->all(), [
            'service_type_name' => 'required',
            'service_category_id' => 'required'
        ]);

        if($validator->fails()) {
            $errors = $validator->messages()->toJson();
            return response()->json([
                'success' => false,
                'message' => $errors
            ]);
        }

        $categories = ServiceCategory::find($request->service_category_id);
        if(empty($categories)) {
            return response()->json([
                'success' => false,
                'message' => 'Такой категории не существует. Попытка подмены данных.'
            ], 400);
        }

        $servicePrice = $request->service_price;

        if((bool) $request->approximate === true)
            $servicePrice = null;

        $service = new ServiceType($request->all());
        $service->service_category_id = $request->service_category_id;        
        $service->service_price = $servicePrice;
        $service->sto_id = $sto->id;
        $service->defect_option_id = $request->defect_option_id;
        $service->save();

        return response()->json([
            'success' => true, 
            'message' => 'Услуга успешно создана.',
            'service' => $service
        ]);
    }

    public function updateServiceType(Request $request, $sto_slug, $id) 
    {
        $sto = Sto::where('slug', $sto_slug)->first();

        $categories = ServiceCategory::find($request->service_category_id);
        if(empty($categories)) {
            return response()->json([
                'success' => false,
                'message' => 'Такой категории не существует. Попытка подмены данных.'
            ], 400);
        }

        $servicePrice = $request->service_price;

        if((bool) $request->approximate === true)
            $servicePrice = null;

        $service = ServiceType::where('id', $id)->update([
            'service_type_name' => $request->service_type_name,
            'service_category_id' => $request->service_category_id,
            'service_price' => $servicePrice,
            'defect_option_id' => $request->defect_option_id
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Услуга успешно изменена.',
            'service' => $service
        ]);
    }

    /**
     * Get all service types from database for a specific STO.
     * 
     * @param   string $sto_slug
     * 
     * @return  \Illuminate\Http\Response
     */
    public function getTypes($sto_slug)
    {
        $sto = Sto::where('slug', $sto_slug)->first();
        $types = ServiceType::select('service_types.*', 'service_type_name', 'service_price', 'service_category_name')
                            ->join('service_categories', 'service_types.service_category_id', '=', 'service_categories.id')
                            ->where('service_types.sto_id', $sto->id)->get();

        return response()->json($types);
    }

    /**
     * Get invoice based on defect options.
     * 
     * @param   string $sto_slug
     * 
     * @return  \Illuminate\Http\Response
     */
    public function invoice(Request $request, $sto_slug) 
    {
        $sto = Sto::where('slug', $sto_slug)->first();

        $defectOptionCondition = '';
        $defectOptions = $request->defect_options;
        foreach($defectOptions as $option) {
            $defectOptionCondition .= $option . ' ';
        }

        $defectOptionCondition = str_replace(' ', ',', trim($defectOptionCondition));

        $invoice = ServiceType::select('service_types.id', 'service_type_name', 'service_price', 'service_category_name', 'defect_option_id')
                                ->join('service_categories', 'service_types.service_category_id', '=', 'service_categories.id')
                                ->where('service_types.sto_id', $sto->id)
                                ->whereIn('defect_option_id', $defectOptions)->get();

        return response()->json($invoice);                        
    }
}
