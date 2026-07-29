<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VideoTutorial;
use App\Models\VideoWatchRecord;
use Illuminate\Http\Request;

class VideoWatchRecordController extends Controller
{
    /**
     * GET /video-watch-records
     * Global, filterable audit log — "who watched what, via which flow, when".
     */
    public function index(Request $request)
    {
        $query = VideoWatchRecord::query()->with([
            'driver:id,name,email,phone',
            'videoTutorial:id,title',
            'assistedBy:id,name',
            'firstRequiredByCargo:id,dispatch_id',
        ]);

        $query->when($request->filled('driver_id'), fn ($q) => $q->where('driver_id', $request->input('driver_id')));
        $query->when($request->filled('video_tutorial_id'), fn ($q) => $q->where('video_tutorial_id', $request->input('video_tutorial_id')));
        $query->when($request->filled('cargo_detail_id'), fn ($q) => $q->where('first_required_by_cargo_id', $request->input('cargo_detail_id')));
        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')));
        $query->when($request->has('is_assisted'), fn ($q) => $q->where('is_assisted', $request->boolean('is_assisted')));
        $query->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')));
        $query->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')));

        $perPage = $request->query('perPage') ?? 20;

        return response()->json($query->latest()->paginate($perPage));
    }

    /**
     * GET /drivers/{user}/video-watch-records
     * Full watch history for one driver, across every trip/video.
     */
    public function forDriver(User $user)
    {
        $records = VideoWatchRecord::where('driver_id', $user->id)
            ->with(['videoTutorial:id,title', 'assistedBy:id,name', 'firstRequiredByCargo:id,dispatch_id'])
            ->latest()
            ->get();

        return response()->json(['video_watch_records' => $records]);
    }

    /**
     * GET /video-tutorials/{videoTutorial}/watch-records
     * Adoption report for one tutorial video — who has/hasn't watched it.
     */
    public function forVideo(VideoTutorial $videoTutorial, Request $request)
    {
        $query = VideoWatchRecord::where('video_tutorial_id', $videoTutorial->id)
            ->with(['driver:id,name,email,phone', 'assistedBy:id,name']);

        $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')));

        $perPage = $request->query('perPage') ?? 20;

        return response()->json($query->latest()->paginate($perPage));
    }
}