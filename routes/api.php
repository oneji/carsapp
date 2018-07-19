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

Route::get('/check', function() {
    return response()->json([
        'success' => true,
        'message' => 'API is working...'
    ]);
});

// General routes
Route::post('auth/login', 'AuthController@login');    
Route::post('auth/register', 'AuthController@register');
Route::get('/token/refresh', 'AuthController@refreshToken');
Route::get('/token/check', 'AuthController@checkToken');

Route::group(['middleware' => ['jwt.auth']], function() {
    Route::post('auth/logout', 'AuthController@logout');
    Route::get('/me', 'AuthController@user');    
    Route::get('/user/acl', 'AuthController@acl');
    Route::get('/projects', 'ClientHomeController@fetchUserProjects');
    Route::get('/all-cars', 'ClientHomeController@getAllCars');
    Route::get('/all-drivers', 'ClientHomeController@getAllDrivers');
    Route::put('/password/change', 'AuthController@changePassword');    
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
        Route::get('/{slug}/cars/sold', 'CarController@getSoldCars');
        Route::get('/{slug}/cars/{car}/edit', 'CarController@edit');
        Route::get('/{slug}/cars/{car}/card', 'CarController@getCardInfo');
        
        Route::post('/{slug}/cars', 'CarController@store');
        Route::post('/{slug}/cars/drivers', 'CarController@bindDriver'); 
        Route::post('/{slug}/cars/{car}/update', 'CarController@update');
        Route::post('/{slug}/cars/{car}/card', 'CarController@createCard');
        Route::post('/{slug}/cars/{car}/card/{card}/fines', 'CarController@addFine');

        Route::put('/{slug}/cars/drivers', 'CarController@unbindDriver');  
        Route::put('/{slug}/cars/reserve/put', 'CarController@reserveCar');     
        Route::put('/{slug}/cars/reserve/get', 'CarController@backFromReserve');
        Route::put('/{slug}/cars/{car}/sold/{status}', 'CarController@changeSoldStatus');
        Route::put('/{slug}/cars/{car}/fines/{fine}/{status}', 'CarController@changePaidFineStatus');
        // Driver routes
        Route::get('/{slug}/drivers', 'DriverController@get');
        Route::post('/{slug}/drivers', 'DriverController@store');
        Route::post('/{slug}/drivers/{driver}/queue', 'DriverController@addToQueue');
        Route::delete('/{slug}/drivers/{driver}/queue', 'DriverController@backFromQueue');
        Route::get('/{slug}/drivers/queue', 'DriverController@getQueue');
        Route::get('/{slug}/drivers/{driver}/edit', 'DriverController@edit');
        Route::post('/{slug}/drivers/{driver}/update', 'DriverController@update');
        // Request routes
        Route::get('/{slug}/sto-list', 'StoRequestController@get');
        Route::get('/{slug}/requests/repair', 'RepairRequestController@get');

        Route::post('/{slug}/sto-list/{sto}', 'StoRequestController@store');
        Route::post('/{slug}/requests/repair', 'RepairRequestController@store');

        Route::delete('/{slug}/requests/{request}', 'StoRequestController@cancel');
        // Statistics routes
        Route::get('/{slug}/statictics', 'ClientHomeController@getStatistics');
    });    
});

// Sto routes
Route::group(['namespace' => 'Sto', 'prefix' => 'sto'], function() {
    Route::group(['middleware' => ['jwt.auth']], function() {
        // Request routes
        Route::get('/{slug}/requests', 'StoRequestController@get');
        Route::get('/{slug}/requests/repair', 'RepairRequestController@get');

        Route::post('/{slug}/requests/{request}/queue', 'RepairRequestController@toQueue');
        Route::put('/{slug}/requests/{request}', 'StoRequestController@accept');
        // Company routes
        Route::get('/{slug}/companies', 'CompanyController@get');
        Route::get('/{slug}/companies/{company}/cars', 'CompanyController@getCars');
        // Services routes
        Route::get('/{slug}/services/categories', 'ServiceController@getCategories');
        Route::get('/{slug}/services/types', 'ServiceController@getTypes');
        
        Route::post('/{slug}/services/categories', 'ServiceController@storeCategory');
        Route::post('/{slug}/services/types', 'ServiceController@storeService');
        Route::post('/{slug}/services/invoice', 'ServiceController@invoice');
        // Car routes
        Route::post('/{slug}/cars', 'CarController@store');
        Route::post('/{slug}/cars/{car}/card', 'CarCardController@createCard');
        Route::post('/{slug}/cars/{car}/attachments', 'CarController@addAttachments');
        
        Route::get('/{slug}/cars/{car}/card', 'CarCardController@getInfo');
        // Car card routes
        Route::post('/{slug}/cards/{card}/defects', 'CarCardController@saveDefects');
        Route::post('/{slug}/cards/{card}/comments', 'CarCardController@storeComment');
        // Defect act 
        Route::get('/{slug}/cards/{card}/defects/acts', 'DefectActController@get');
        Route::post('/{slug}/cards/{card}/defects/acts', 'DefectActController@store');
        // Defect routes
        Route::get('/{slug}/defects/all', 'DefectController@getAll');
        Route::get('/{slug}/defects/categories', 'DefectController@getOptions');
        Route::get('/{slug}/defects/types', 'DefectController@getTypes');
        Route::get('/{slug}/defects/info', 'DefectController@getFullInfo'); 

        Route::post('/{slug}/defects', 'DefectController@storeDefect');   
        Route::post('/{slug}/defects/types', 'DefectController@storeType');   
        Route::post('/{slug}/defects/options', 'DefectController@storeOption');   
        
        Route::put('/{slug}/defects/{defect}', 'DefectController@updateDefect');
        Route::put('/{slug}/defects/types/{type}', 'DefectController@updateType');
        Route::put('/{slug}/defects/options/{option}', 'DefectController@updateOption');
        // Engine routes
        Route::get('/{slug}/engine/types', 'EngineTypeController@get');
        Route::get('/{slug}/engine/transmissions', 'TransmissionController@get');

        Route::post('/{slug}/engine/types', 'EngineTypeController@store');
        Route::post('/{slug}/engine/transmissions', 'TransmissionController@store');

        Route::put('/{slug}/engine/types/{type}', 'EngineTypeController@update');
        Route::put('/{slug}/engine/transmissions/{transmission}', 'TransmissionController@update');
    });
});