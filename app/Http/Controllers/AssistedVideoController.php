<?php

namespace App\Http\Controllers;

use App\Models\CargoDetail;
use App\Models\VideoTutorial;
use App\Models\VideoWatchRecord;
use App\Services\VideoTestService;
use App\Services\VideoWatchService;
use App\Traits\HasStoreFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AssistedVideoController extends Controller
{
    use HasStoreFile;

    public function __construct(
        private VideoWatchService $videoWatchService,
        private VideoTestService $videoTestService,
    ) {}

    // Requirement #8: driver has no phone, so the dispatcher facilitates
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

    // Driver still answers themselves - this is the same borrowed-device
    // session as selfie()/complete(), not the dispatcher taking the test.
    public function showTest(
        CargoDetail $cargoDetail,
        VideoTutorial $videoTutorial,
    ) {
        $this->resolveDriver($cargoDetail, $videoTutorial);

        $test = $videoTutorial
            ->videoTest()
            ->with("questions.options")
            ->firstOrFail();

        return response()->json(["video_test" => $test]);
    }

    public function submitTest(
        Request $request,
        CargoDetail $cargoDetail,
        VideoTutorial $videoTutorial,
    ) {
        $driver = $this->resolveDriver($cargoDetail, $videoTutorial);

        $record = VideoWatchRecord::where("driver_id", $driver->id)
            ->where("video_tutorial_id", $videoTutorial->id)
            ->firstOrFail();

        $test = $videoTutorial->videoTest()->with("questions")->firstOrFail();

        $fields = Validator::make($request->all(), [
            "answers" => "required|array|size:" . $test->questions->count(),
            "answers.*" => "required|integer|exists:video_test_question_options,id",
        ]);

        if ($fields->fails()) {
            return response()->json(["error" => $fields->errors()], 422);
        }

        $answers = $request->input("answers");
        $missingQuestionIds = $test->questions
            ->pluck("id")
            ->diff(array_keys($answers));

        if ($missingQuestionIds->isNotEmpty()) {
            return response()->json(
                ["error" => "Every question must be answered."],
                422,
            );
        }

        $attempt = $this->videoTestService->submitAttempt(
            $record,
            $test,
            $answers,
        );

        return response()->json(["video_test_attempt" => $attempt], 201);
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
