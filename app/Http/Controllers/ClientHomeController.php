<?php

namespace App\Http\Controllers;

use App\Car;
use App\Company;
use App\DriverQueue;
use JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                $query->select('cars.*', 'shape_name', 'brand_name', 'model_name', 'engine_type_name', 'transmission_name')
                    ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                    ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                    ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                    ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                    ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
                    ->where('sold', 0)  
                    ->with([ 'drivers' => function($query) {
                        $query->where('active', 1)->get();
                    }, 'card', 'companies' ])->get();
            },
            'companies.drivers.cars' => function($query) {
                $query->select('cars.*', 'shape_name', 'brand_name', 'model_name', 'engine_type_name', 'transmission_name')
                    ->join('car_shapes', 'car_shapes.id', '=', 'cars.shape_id')
                    ->join('car_models', 'car_models.id', '=', 'cars.model_id')
                    ->join('car_brands', 'car_brands.id', '=', 'cars.brand_id')
                    ->join('engine_types', 'engine_types.id', '=', 'cars.engine_type_id')
                    ->join('transmissions', 'transmissions.id', '=', 'cars.transmission_id')
                    ->with('attachments', 'card.comments.user', 'card.defect_acts.defect_options.defect', 'card.defect_acts.equipment', 'card.fines.attachments')
                    ->where('sold', 0)
                    ->get();
            }]);

        return response()->json($user);
    }

    public function getAllDrivers()
    {
        $boundDrivers = DB::table('car_driver')->where('active', 1)->pluck('driver_id')->all();

        $user = JWTAuth::parseToken()->authenticate()->load([
            'companies.drivers.queue'
        ]);

        return response()->json($user);
    }

    public function getStatistics()
    {
        $user = JWTAuth::parseToken()->authenticate()->load(['companies.cars', 'companies.drivers']);
        $carsCount = 0;
        $driversCount = 0;
        $reservedCarsCount = 0;
        $companiesCount = count($user->companies);
        $queuedDriversCount = count(DriverQueue::all());
        
        foreach($user->companies as $company) {
            foreach($company->cars as $car) {
                if($car->sold === 0) {
                    if($car->reserved === 0) {
                        $carsCount++;
                    } else {
                        $reservedCarsCount++;
                    }
                } 
            }

            foreach($company->drivers as $driver) {
                $driversCount++;
            }
        }


        return response()->json([
            'companiesCount' => $companiesCount,
            'carsCount' => $carsCount,
            'reservedCarsCount' => $reservedCarsCount,
            'driversCount' => $driversCount,
            'queuedDriversCount' => $queuedDriversCount,
            'user' => $user
        ]);
    }
}
