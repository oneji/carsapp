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

Route::get('/fill-prices', 'Sto\DefectController@fillServicePrices');

// General routes
Route::post('auth/login', 'AuthController@login');    
Route::post('auth/register', 'AuthController@register');
Route::get('/token/refresh', 'AuthController@refreshToken');
Route::get('/token/check', 'AuthController@checkToken');
Route::get('rt-act/files/download', 'RtActController@downloadFile');
Route::get('defect-acts/files/download', 'Sto\DefectActController@downloadFile');


Route::group(['middleware' => ['jwt.auth']], function() {
    Route::post('auth/logout', 'AuthController@logout');
    Route::get('/me', 'AuthController@user');    
    Route::get('/user/acl', 'AuthController@acl');
    Route::get('/statistics', 'ClientHomeController@getStatistics');
    Route::get('/projects', 'ClientHomeController@fetchUserProjects');
    Route::get('/all-cars', 'ClientHomeController@getAllCars');
    Route::get('/all-drivers', 'ClientHomeController@getAllDrivers');
    Route::put('/password/change', 'AuthController@changePassword');    

    Route::get('/car/{car}/consumables', 'ConsumableController@get');
    Route::post('/car/{car}/consumables', 'ConsumableController@update');
    Route::post('/car/{car}/consumables/create', 'ConsumableController@store');

    // RT act
    Route::get('generateActFile', 'RtActController@generateActFile');
    Route::get('rt-act/{act}/sendActFile', 'RtActController@sendActFile');
    Route::get('rt-act/getById/{act}', 'RtActController@getById');
    Route::get('rt-act/info', 'RtActController@getFullInfo');

    Route::post('rt-act', 'RtActController@store');
    Route::post('rt-act/draft', 'RtActController@saveToDraft');
    Route::post('rt-act/edit/{act}', 'RtActController@edit');
    Route::post('rt-act/{act}/files/add', 'RtActController@addMoreFiles');
    
    // RT act checklists
    Route::get('rt-act/checklists', 'RtActChecklistController@get');
    Route::get('rt-act/all', 'RtActChecklistController@getChecklistsAndChecklistItems');
    Route::post('rt-act/checklists', 'RtActChecklistController@store');
    Route::put('rt-act/checklists/{checklist}', 'RtActChecklistController@update');
    // RT act checklist items
    Route::get('rt-act/checklists/items', 'RtActChecklistItemController@get');
    Route::post('rt-act/checklists/items', 'RtActChecklistItemController@store');
    Route::put('rt-act/checklists/items/{item}', 'RtActChecklistItemController@update');

    // Done act
    Route::get('done-acts/{act}', 'DoneActController@getById');
    Route::post('done-acts', 'DoneActController@store');
    Route::post('done-acts/{act}', 'DoneActController@send');
    Route::put('done-acts/defects/{defect}/markAsDone', 'DoneActController@markDetailAsDone');
    Route::put('done-acts/{act}/confirmAndClose', 'DoneActController@confirmAndClose');
}); 

// Admin routes
Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function() {
    Route::group(['middleware' => ['jwt.auth']], function() {
        // Company routes
        Route::get('/companies', 'CompanyController@get');
        Route::post('/companies', 'CompanyController@store');
        Route::post('/companies/bind/{user}', 'CompanyController@bindUser');
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
        Route::post('/{slug}/requests/{request}/archive', 'RepairRequestController@archive');

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
        Route::post('/{slug}/requests/{request}/sto', 'RepairRequestController@toSto');
        Route::post('/{slug}/requests/{request}/repair-done', 'RepairRequestController@repairDone');
        Route::put('/{slug}/requests/{request}', 'StoRequestController@accept');
        // Company routes
        Route::get('/{slug}/companies', 'CompanyController@get');
        Route::get('/{slug}/companies/{company}/cars', 'CompanyController@getCars');
        // Services routes
        Route::get('/{slug}/prices', 'ServiceController@getPrices');
        Route::post('/{slug}/prices', 'ServiceController@setPrice');
        Route::post('/{slug}/hhprice', 'ServiceController@setHumanHourPriceForAll');
        
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
        Route::get('/{slug}/defect-acts/{act}', 'DefectActController@getById');
        Route::get('/{slug}/defect-acts/{act}/sendActFile', 'DefectActController@sendActFile');    

        Route::post('/{slug}/defect-acts/{act}/files/add', 'DefectActController@addMoreFiles');
        Route::post('/{slug}/cards/{card}/defects/acts', 'DefectActController@store');

        // Defect routes
        Route::get('/{slug}/defects/all', 'DefectController@getAll');
        Route::get('/{slug}/defects/categories', 'DefectController@getOptions');
        Route::get('/{slug}/defects/types', 'DefectController@getTypes');
        Route::get('/{slug}/defects/conclusions', 'DefectController@getConclusions');
        Route::get('/{slug}/defects/info', 'DefectController@getFullInfo'); 

        Route::post('/{slug}/defects', 'DefectController@storeDefect');   
        Route::post('/{slug}/defects/types', 'DefectController@storeType');   
        Route::post('/{slug}/defects/options', 'DefectController@storeOption');   
        Route::post('/{slug}/defects/conclusions', 'DefectController@storeConclusion');   
        
        Route::put('/{slug}/defects/{defect}', 'DefectController@updateDefect');
        Route::put('/{slug}/defects/types/{type}', 'DefectController@updateType');
        Route::put('/{slug}/defects/options/{option}', 'DefectController@updateOption');
        Route::put('/{slug}/defects/conclusions/{conclusion}', 'DefectController@updateConclusion');

        Route::delete('/{slug}/defects/types/{type}', 'DefectController@deleteType');
        Route::delete('/{slug}/defects/{defect}', 'DefectController@deleteDefect');
        Route::delete('/{slug}/defects/options/{option}', 'DefectController@deleteOption');
        Route::delete('/{slug}/defects/conclusions/{conclusion}', 'DefectController@deleteConclusion');
        // Engine routes
        Route::get('/{slug}/engine/types', 'EngineTypeController@get');
        Route::get('/{slug}/engine/transmissions', 'TransmissionController@get');

        Route::post('/{slug}/engine/types', 'EngineTypeController@store');
        Route::post('/{slug}/engine/transmissions', 'TransmissionController@store');

        Route::put('/{slug}/engine/types/{type}', 'EngineTypeController@update');
        Route::put('/{slug}/engine/transmissions/{transmission}', 'TransmissionController@update');
    });
});