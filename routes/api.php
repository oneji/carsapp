<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api'], function() {
    Route::post('auth/login', 'AuthController@login');    
    Route::post('auth/register', 'AuthController@register');

    Route::group(['middleware' => ['jwt.auth']], function() {
        Route::post('auth/logout', 'AuthController@logout');
        Route::get('/me', 'AuthController@user');

        // Company routes
        Route::get('/companies', 'CompanyController@get')->name('api.companies.get');
        Route::post('/companies', 'CompanyController@store')->name('api.companies.store');
    }); 
});