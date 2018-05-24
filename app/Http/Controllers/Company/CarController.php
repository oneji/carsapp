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
        $cars = Car::all();
        return response()->json($cars);
    }
}
