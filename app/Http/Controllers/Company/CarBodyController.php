<?php

namespace App\Http\Controllers\Company;

use App\CarShape;
use App\CarModel;
use App\CarBrand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class CarBodyController extends Controller
{
    /**
     * Get all car shapes.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getShapes() 
    {
        $shapes = CarShape::all();
        return response()->json($shapes);
    }

    /**
     * Store a newly created car shape.
     * 
     * @param   \Illuminate\Http\Request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function storeShape(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shape_name' => 'required'
        ]);

        if($validator->fails()) {
            $errors = $validator->messages()->toJson();

            return response()->json([
                'success' => true,
                'message' => $errors
            ]);
        }

        $shape = new CarShape();
        $shape->shape_name = $request->shape_name;
        $shape->save();

        return response()->json([
            'success' => true,
            'message' => 'Кузов успешно создан'
        ]);
    }

    /**
     * Get all car models.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getModels() 
    {
        $models = CarModel::all();
        return response()->json($models);
    }

    /**
     * Store a newly created car model.
     * 
     * @param   \Illuminate\Http\Request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function storeModel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'model_name' => 'required'
        ]);

        if($validator->fails()) {
            $errors = $validator->messages()->toJson();

            return response()->json([
                'success' => true,
                'message' => $errors
            ]);
        }

        $model = new CarModel();
        $model->model_name = $request->model_name;
        $model->save();

        return response()->json([
            'success' => true,
            'message' => 'Модель успешно создан'
        ]);
    }

    /**
     * Get all car brands.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getBrands() 
    {
        $brands = CarBrand::all();
        return response()->json($brands);
    }

    /**
     * Store a newly created car brand.
     * 
     * @param   \Illuminate\Http\Request
     * 
     * @return  \Illuminate\Http\Response
     */
    public function storeBrand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_name' => 'required'
        ]);

        if($validator->fails()) {
            $errors = $validator->messages()->toJson();

            return response()->json([
                'success' => true,
                'message' => $errors
            ]);
        }

        $brand = new CarBrand();
        $brand->brand_name = $request->brand_name;
        $brand->save();

        return response()->json([
            'success' => true,
            'message' => 'Бренд успешно создан'
        ]);
    }
}
