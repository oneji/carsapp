<?php

namespace App\Http\Controllers\Company;

use App\CarShape;
use App\CarModel;
use App\CarBrand;
use App\EngineType;
use App\Transmission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class CarBodyController extends Controller
{
    /**
     * Get all car body info.
     * 
     * @return  \Illuminate\Http\Response
     */
    public function getCarBodyInfo() 
    {
        $shapes = CarShape::orderBy('shape_name')->get();
        $brands = CarBrand::orderBy('brand_name')->get();
        $models = CarModel::select(['car_models.id', 'car_models.model_name', 'car_brands.brand_name'])
                            ->join('car_brands', 'car_brands.id', '=', 'car_models.brand_id')
                            ->get();
        $engine_types = EngineType::orderBy('engine_type_name')->get();
        $transmissions = Transmission::orderBy('transmission_name')->get();

        return response()->json([
            'shapes' => $shapes,
            'brands' => $brands,
            'models' => $models,
            'engine_types' => $engine_types,
            'transmissions' => $transmissions
        ]);
    }
    /**
     * Get all car shapes.
     * 
     * @return  \Illuminate\Http\Response
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
            'message' => 'Кузов успешно создан',
            'shape' => $shape
        ]);
    }

    /**
     * Remove car shape from database.
     * 
     * @param   int $id
     * 
     * @return  \Illuminate\Http\Response
     */
    public function destroyShape($id)
    {
        $shape = CarShape::destroy($id);

        return response()->json([
            'success' => true,
            'message' => 'Кузов успешно удален'
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
        $model->brand_id = $request->brand_id;
        $model->save();

        return response()->json([
            'success' => true,
            'message' => 'Модель успешно создан',
            'model' => $model
        ]);
    }

    /**
     * Remove car model from database.
     * 
     * @param   int $id
     * 
     * @return  \Illuminate\Http\Response
     */
    public function destroyModel($id)
    {
        $model = CarModel::destroy($id);

        return response()->json([
            'success' => true,
            'message' => 'Модель успешно удалена'
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
            'message' => 'Бренд успешно создан',
            'brand' => $brand
        ]);
    }

    /**
     * Remove car brand from database.
     * 
     * @param   int $id
     * 
     * @return  \Illuminate\Http\Response
     */
    public function destroyBrand($id)
    {
        $brand = CarBrand::destroy($id);

        return response()->json([
            'success' => true,
            'message' => 'Марка успешно удалена'
        ]);
    }
}
