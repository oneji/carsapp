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

// General routes
Route::post('auth/login', 'AuthController@login');    
Route::post('auth/register', 'AuthController@register');
Route::get('/token/refresh', 'AuthController@refreshToken');
Route::get('/token/check', 'AuthController@checkToken');

Route::group(['middleware' => ['jwt.auth']], function() {
    Route::post('auth/logout', 'AuthController@logout');
    Route::get('/me', 'AuthController@user');    
}); 

Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function() {
    Route::group(['middleware' => ['jwt.auth']], function() {
        // Company routes
        Route::get('/companies', 'CompanyController@get');
        Route::post('/companies', 'CompanyController@store');
        Route::post('/companies/{company}/bind/{user}', 'CompanyController@bindUser');
        // Sto routes
        Route::get('/stos', 'StoController@get');
        Route::post('/stos', 'StoController@store');
        Route::post('/stos/{sto}/bind/{user}', 'StoController@bindUser');
        // User routes
        Route::get('/users', 'UserController@getAll');
        Route::get('/users/{user}', 'UserController@getByID');
        Route::delete('/users/{user}', 'UserController@destroy');
        Route::post('/users', 'UserController@store');
        Route::put('/users/{user}', 'UserController@update');
        // Acl routes
        Route::get('/acl', 'AclController@get');
        Route::put('/acl', 'AclController@update');
        Route::post('/acl/roles', 'AclController@createRole');
        Route::post('/acl/permissions', 'AclController@createPermission');
    }); 
});

Route::group(['namespace' => 'Company', 'prefix' => 'company'], function() {
    Route::group(['middleware' => ['jwt.auth']], function() {
        // Car routes
        Route::get('/{slug}/cars', 'CarController@get');
        Route::post('/{slug}/cars', 'CarController@store');
        // Car body routes
        Route::get('/cars/body/info', 'CarBodyController@getCarBodyInfo');
        Route::get('/cars/shapes', 'CarBodyController@getShapes');
        Route::post('cars/shapes', 'CarBodyController@storeShape');
        Route::delete('/cars/shapes/{shape}', 'CarBodyController@destroyShape');
        Route::get('/cars/models', 'CarBodyController@getModels');
        Route::post('cars/models', 'CarBodyController@storeModel');
        Route::delete('/cars/models/{model}', 'CarBodyController@destroyModel');
        Route::get('/cars/brands', 'CarBodyController@getBrands');
        Route::post('cars/brands', 'CarBodyController@storeBrand');
        Route::delete('/cars/brands/{brand}', 'CarBodyController@destroyBrand');
        // Driver routes
        Route::post('/drivers', 'DriverController@store');
        Route::get('/drivers', 'DriverController@get');
    });    
});