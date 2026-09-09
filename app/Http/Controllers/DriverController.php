<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VideoTutorial;
use App\Services\VideoTestService;
use App\Services\VideoWatchService;
use App\Traits\HasStoreFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * The driver-first entry point for video assistance: a dedicated list of
 * drivers (independent of any single trip), and the same assisted-video
 * procedure AssistedVideoController offers per-trip - scoped by driver_id
 * instead of cargo_detail_id, since watch progress is already global per
 * driver (VideoWatchService), not per trip. See CONTEXT.md's "Driver" and
 * "Assisted flow" entries.
 */
class DriverController extends Controller
{
    use HasStoreFile;

    public function __construct(
        private VideoWatchService $videoWatchService,
        private VideoTestService $videoTestService,
    ) {}

    public function index(Request $request)
    {
        $request->validate([
            'video_status' => 'nullable|in:pending,completed',
            'group_id' => 'nullable|integer|exists:groups,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $user = Auth::user();
        $hasGlobalVisibility = $user->is_admin == 1 || $user->role === 'Ground Surveyor';

        $query = User::where('role', 'Driver');

        if (!$hasGlobalVisibility) {
            $query->whereHas(
                'assignedTrips',
                fn($q) => $q->where('group_id', $user->group_id),
            );
        } elseif ($request->filled('group_id')) {
            $query->whereHas(
                'assignedTrips',
                fn($q) => $q->where('group_id', $request->input('group_id')),
            );
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }
        if ($request->filled('mobile')) {
            $query->where('phone', 'like', '%' . $request->input('mobile') . '%');
        }

        if ($request->filled('video_status')) {
            $query->where('video_status', $request->input('video_status'));
        }

        return response()->json(
            $query->orderByDesc('id')->paginate($request->input('per_page', 15)),
        );
    }

    public function pendingVideos(User $driver)
    {
        abort_unless($driver->role === 'Driver', 404, 'Driver not found.');
        $this->authorizeDriverVisibility($driver);

        return response()->json([
            'pending_video_tutorials' => $this->videoWatchService->pendingVideoTutorialsForDriver($driver),
        ]);
    }

    // Same assisted procedure as AssistedVideoController, entered from the
    // drivers list instead of a specific trip.
    public function assistedSelfie(Request $request, User $driver, VideoTutorial $videoTutorial)
    {
        $this->resolveAssistableDriver($driver, $videoTutorial);

        $fields = Validator::make($request->all(), [
            'selfie' => 'required|image',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($fields->fails()) {
            return response()->json(['error' => $fields->errors()], 422);
        }

        $selfiePath = $this->storeFile($request, 'selfie', 'driver_selfies');

        $record = $this->videoWatchService->startWatch(
            $driver,
            $videoTutorial,
            $selfiePath,
            $request->input('latitude'),
            $request->input('longitude'),
            assisted: true,
            assistedBy: Auth::user(),
        );

        return response()->json(['video_watch_record' => $record], 201);
    }

    public function assistedComplete(User $driver, VideoTutorial $videoTutorial)
    {
        $this->resolveAssistableDriver($driver, $videoTutorial);

        $record = $this->videoWatchService->completeWatch($driver, $videoTutorial);

        return response()->json(['video_watch_record' => $record]);
    }

    public function assistedShowTest(User $driver, VideoTutorial $videoTutorial)
    {
        $this->resolveAssistableDriver($driver, $videoTutorial);

        $test = $videoTutorial->videoTest()->with('questions.options')->firstOrFail();

        return response()->json(['video_test' => $test]);
    }

    public function assistedSubmitTest(Request $request, User $driver, VideoTutorial $videoTutorial)
    {
        $this->resolveAssistableDriver($driver, $videoTutorial);

        $record = \App\Models\VideoWatchRecord::where('driver_id', $driver->id)
            ->where('video_tutorial_id', $videoTutorial->id)
            ->firstOrFail();

        $test = $videoTutorial->videoTest()->with('questions')->firstOrFail();

        $fields = Validator::make($request->all(), [
            'answers' => 'required|array|size:' . $test->questions->count(),
            'answers.*' => 'required|integer|exists:video_test_question_options,id',
        ]);

        if ($fields->fails()) {
            return response()->json(['error' => $fields->errors()], 422);
        }

        $answers = $request->input('answers');
        $missingQuestionIds = $test->questions->pluck('id')->diff(array_keys($answers));

        if ($missingQuestionIds->isNotEmpty()) {
            return response()->json(['error' => 'Every question must be answered.'], 422);
        }

        $attempt = $this->videoTestService->submitAttempt($record, $test, $answers);

        return response()->json(['video_test_attempt' => $attempt], 201);
    }

    private function resolveAssistableDriver(User $driver, VideoTutorial $videoTutorial): void
    {
        abort_unless($driver->role === 'Driver', 404, 'Driver not found.');
        $this->authorizeDriverVisibility($driver);

        abort_unless(
            $this->videoWatchService->isVideoApplicableToDriver($driver, $videoTutorial),
            422,
            'This video is not assigned to any of this driver\'s trips.',
        );
    }

    /**
     * A dispatcher may only act on a driver who has at least one trip
     * visible to them - same rule index() filters by, applied per-driver
     * for the show/action endpoints.
     */
    private function authorizeDriverVisibility(User $driver): void
    {
        $user = Auth::user();

        if ($user->is_admin == 1 || $user->role === 'Ground Surveyor') {
            return;
        }

        abort_unless(
            $driver->assignedTrips()->where('group_id', $user->group_id)->exists(),
            404,
            'Driver not found.',
        );
    }
}
