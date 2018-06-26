<?php

namespace App\Http\Controllers;

use App\Car;
use JWTAuth;
use Illuminate\Http\Request;

class ClientHomeController extends Controller
{
    public function fetchUserProjects()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user->load(['roles', 'permissions', 'companies', 'stos']);

        return response()->json($user);
    }

    public function getAllCars()
    {
        $user = JWTAuth::parseToken()->authenticate()->load([
            'companies.cars' => function($query) {
                $query->select('cars.id as id', 'year', 'number', 'shape_name', 'brand_name', 'model_name', 'milage', 'vin_code', 'cover_image', 'engine_capacity', 'engine_type_name', 'transmission_name')
                    ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                    ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                    ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                    ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                    ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
                    ->with([ 'drivers', 'card' ])->get();
            }
        ]);

        return response()->json($user);
    }
}
