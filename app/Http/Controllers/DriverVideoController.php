<?php

namespace App\Http\Controllers;

use App\Models\VideoTutorial;
use App\Models\VideoWatchRecord;
use App\Services\VideoTestService;
use App\Services\VideoWatchService;
use App\Traits\HasStoreFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DriverVideoController extends Controller
{
    use HasStoreFile;

    public function __construct(
        private VideoWatchService $videoWatchService,
        private VideoTestService $videoTestService,
    ) {}

    // Only videos, never cargo/dispatch data - satisfies the "drivers see videos only" requirement.
    // One row per video; `status` tells the frontend whether to watch it
    // or take its test - see VideoWatchService::pendingVideoTutorialsForDriver().
    public function pending()
    {
        $videos = $this->videoWatchService->pendingVideoTutorialsForDriver(
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

    // Questions + options only - is_correct stays hidden (VideoTestQuestionOption default).
    public function showTest(VideoTutorial $videoTutorial)
    {
        $this->authorizeVideoAccess($videoTutorial);

        $test = $videoTutorial
            ->videoTest()
            ->with("questions.options")
            ->firstOrFail();

        return response()->json(["video_test" => $test]);
    }

    public function submitTest(Request $request, VideoTutorial $videoTutorial)
    {
        $this->authorizeVideoAccess($videoTutorial);

        $driver = Auth::user();
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

    private function authorizeVideoAccess(VideoTutorial $videoTutorial): void
    {
        abort_unless(
            $this->videoWatchService->isVideoApplicableToDriver(
                Auth::user(),
                $videoTutorial,
            ),
            403,
            "This video is not assigned to you.",
        );
    }
}
