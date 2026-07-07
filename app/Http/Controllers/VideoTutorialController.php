<?php

namespace App\Http\Controllers;

use App\Models\VideoTutorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VideoTutorialController extends Controller
{
    public function index()
    {
        return response()->json([
            "video_tutorials" => VideoTutorial::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            "title" => "required|string",
            "description" => "nullable|string",
            "video_url" => "required|url",
            "is_active" => "boolean",
        ]);

        if ($fields->fails()) {
            return response()->json(["error" => $fields->errors()], 422);
        }

        $video = VideoTutorial::create([
            "title" => $request->input("title"),
            "description" => $request->input("description"),
            "video_url" => $request->input("video_url"),
            "is_active" => $request->input("is_active", true),
            "created_by" => Auth::id(),
        ]);

        return response()->json(["video_tutorial" => $video], 201);
    }

    public function show($id)
    {
        return response()->json([
            "video_tutorial" => VideoTutorial::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $video = VideoTutorial::findOrFail($id);

        $fields = Validator::make($request->all(), [
            "title" => "sometimes|required|string",
            "description" => "nullable|string",
            "video_url" => "sometimes|required|url",
            "is_active" => "boolean",
        ]);

        if ($fields->fails()) {
            return response()->json(["error" => $fields->errors()], 422);
        }

        $video->fill(
            $request->only(["title", "description", "video_url", "is_active"]),
        );
        $video->save();

        return response()->json(["video_tutorial" => $video]);
    }

    public function destroy($id)
    {
        VideoTutorial::findOrFail($id)->delete();

        return response()->json(null, 204);
    }
}
