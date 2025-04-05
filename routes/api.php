<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('/v1')->group(function () {

    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('channel-partner/register', 'ChannelPartnerController@register');
    Route::post('channel-partner/login', 'ChannelPartnerController@login');
    Route::get('cargo-details_open', 'CargoDetailController@index2');
       Route::apiResources([
            'phase' => 'PhaseController',
            'zone' => 'ZoneController',
            // 'groups' => 'GroupController',
        ]);
    Route::middleware('auth:sanctum')->group(function () {
        // routes/api.php

        Route::apiResources([
            'users' => 'UserController',
            'channel-partner' => 'ChannelPartnerController',
            'groups' => 'GroupController',
            'checklist-masters' => 'ChecklistMasterController',
            'cargo-details' => 'CargoDetailController',
            'checklists' => 'ChecklistController',
            'photographs' => 'PhotographController',
            // 'phase' => 'PhaseController',
            // 'zone' => 'ZoneController',
            'customers' => 'CustomerController',
             'sops' => 'SopController',
        ]);

         Route::post('forgot-password', 'AuthController@forgetPassword');
        Route::post('update-password', 'AuthController@updatePassword');
        Route::post('photographs/{id}', 'PhotographController@update');
        Route::get('groups-count', 'GroupController@count');
        Route::post('groups/{id}', 'GroupController@update');
        Route::get('checklist-masters-count', 'ChecklistMasterController@count');
        Route::get('cargo-details-count', 'CargoDetailController@count');
        Route::post('cargo-details/{id}', 'CargoDetailController@update');
        Route::post('checklists/{id}', 'ChecklistController@update');
        Route::get('checklists-count', 'ChecklistController@count');
        Route::get('photographs-count', 'PhotographController@count');
        Route::get('all-counts', 'UserController@allcounts');

        Route::post('logout', 'AuthController@logout');
    });
});
