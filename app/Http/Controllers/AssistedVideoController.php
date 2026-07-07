<?php

namespace App\Http\Controllers;

use App\Models\CargoDetail;
use App\Models\VideoTutorial;
use App\Services\VideoWatchService;
use App\Traits\HasStoreFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AssistedVideoController extends Controller
{
    use HasStoreFile;

    public function __construct(private VideoWatchService $videoWatchService) {}

    // Requirement #8: driver has no phone, so a staff member facilitates
    // the same selfie -> watch flow from their own device/session.
    public function selfie(
        Request $request,
        CargoDetail $cargoDetail,
        VideoTutorial $videoTutorial,
    ) {
        $driver = $this->resolveDriver($cargoDetail, $videoTutorial);

        $fields = Validator::make($request->all(), [
            "selfie" => "required|image",
            "latitude" => "nullable|numeric",
            "longitude" => "nullable|numeric",
        ]);

        if ($fields->fails()) {
            return response()->json(["error" => $fields->errors()], 422);
        }

        $selfiePath = $this->storeFile($request, "selfie", "driver_selfies");

        $record = $this->videoWatchService->startWatch(
            $driver,
            $videoTutorial,
            $selfiePath,
            $request->input("latitude"),
            $request->input("longitude"),
            assisted: true,
            assistedBy: Auth::user(),
            firstCargo: $cargoDetail,
        );

        return response()->json(["video_watch_record" => $record], 201);
    }

    public function complete(
        CargoDetail $cargoDetail,
        VideoTutorial $videoTutorial,
    ) {
        $driver = $this->resolveDriver($cargoDetail, $videoTutorial);

        $record = $this->videoWatchService->completeWatch(
            $driver,
            $videoTutorial,
        );

        return response()->json(["video_watch_record" => $record]);
    }

    private function resolveDriver(
        CargoDetail $cargoDetail,
        VideoTutorial $videoTutorial,
    ) {
        abort_if(
            !$cargoDetail->driver_id,
            422,
            "This trip has no driver assigned.",
        );

        abort_unless(
            $cargoDetail
                ->videoTutorials()
                ->where("video_tutorials.id", $videoTutorial->id)
                ->exists(),
            422,
            "This video is not assigned to this trip.",
        );

        return $cargoDetail->driver;
    }
}
