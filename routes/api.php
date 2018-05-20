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