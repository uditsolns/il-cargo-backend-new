<?php

namespace App\Http\Controllers;

use App\Models\VideoTutorial;
use App\Services\VideoWatchService;
use App\Traits\HasStoreFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DriverVideoController extends Controller
{
    use HasStoreFile;

    public function __construct(private VideoWatchService $videoWatchService) {}

    // Only videos, never cargo/dispatch data - satisfies the "drivers see videos only" requirement.
    public function pending()
    {
        $videos = $this->videoWatchService->pendingVideosForDriver(
            Auth::user(),
        );

        return response()->json(["video_tutorials" => $videos]);
    }

    public function selfie(Request $request, VideoTutorial $videoTutorial)
    {
        $driver = Auth::user();

        if (
            !$this->videoWatchService->isVideoApplicableToDriver(
                $driver,
                $videoTutorial,
            )
        ) {
            return response()->json(
                ["error" => "This video is not assigned to you."],
                403,
            );
        }

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
        );

        return response()->json(["video_watch_record" => $record], 201);
    }

    public function complete(VideoTutorial $videoTutorial)
    {
        $record = $this->videoWatchService->completeWatch(
            Auth::user(),
            $videoTutorial,
        );

        return response()->json(["video_watch_record" => $record]);
    }
}
