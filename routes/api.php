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

// Admin routes
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
    }); 
});

// Company routes
Route::group(['namespace' => 'Company', 'prefix' => 'company'], function() {
    Route::group(['middleware' => ['jwt.auth']], function() {
        // Car routes
        Route::get('/{slug}/cars', 'CarController@get');
        Route::post('/{slug}/cars', 'CarController@store');
        Route::post('/{slug}/cars/drivers', 'CarController@bindDriver');        
        // Driver routes
        Route::get('/{slug}/drivers', 'DriverController@get');
        Route::post('/{slug}/drivers', 'DriverController@store');
        // Request routes
        Route::get('/{slug}/requests', 'StoRequestController@get');
        Route::post('/{slug}/requests/{sto}', 'StoRequestController@store');
        Route::delete('/{slug}/requests/{request}', 'StoRequestController@cancel');
    });    
});

// Sto routes
Route::group(['namespace' => 'Sto', 'prefix' => 'sto'], function() {
    Route::group(['middleware' => ['jwt.auth']], function() {
        // Request routes
        Route::get('/{slug}/requests', 'StoRequestController@get');
        Route::put('/{slug}/requests/{request}', 'StoRequestController@accept');
        // Company routes
        Route::get('/{slug}/companies', 'CompanyController@get');
        Route::get('/{slug}/companies/{company}/cars', 'CompanyController@getCars');
        // Services routes
        Route::get('/{slug}/services/categories', 'ServiceController@getCategories');
        Route::post('/{slug}/services/categories', 'ServiceController@storeCategory');
        Route::get('/{slug}/services/types', 'ServiceController@getTypes');
        Route::post('/{slug}/services/types', 'ServiceController@storeService');
        Route::post('/{slug}/services/invoice', 'ServiceController@invoice');
        // Car routes
        Route::get('/{slug}/cars/{car}/card', 'CarCardController@getInfo');
        Route::post('/{slug}/cars/{car}/card', 'CarCardController@createCard');
        // Car card routes
        Route::post('/{slug}/cards/{card}/defects', 'CarCardController@saveDefects');
        Route::post('/{slug}/cards/{card}/comments', 'CarCardController@storeComment');
        // Defect routes
        Route::get('/{slug}/defects/categories', 'DefectController@getOptions');
        Route::get('/{slug}/defects/types', 'DefectController@getTypes');
        Route::get('/{slug}/defects/info', 'DefectController@getFullInfo');        
    });
});