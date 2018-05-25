<?php

namespace App\Http\Controllers\Company;

use App\Car;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CarController extends Controller
{
    /**
     * Get all cars from database.
     * 
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        $cars = Car::select('cars.id as id', 'year', 'number', 'shape_name', 'brand_name', 'model_name', 'milage', 'vin_code')
                    ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                    ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                    ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')->get();

        return response()->json($cars);
    }
}
