<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photograph;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PhotographController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $photographs = Photograph::all();
        return response()->json(['photographs' => $photographs]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|max:20',
            'name' => 'required|max:20',
            'photo' => 'required|max:255',
            'time' => 'required|date_format:H:i:s',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'video' => 'required',
            // Add validation for other fields
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 403);
        }

        // Save photo
        $photo = $request->file('photo');
        $photoFileName = $photo->getClientOriginalName();
        $photo->storeAs('public/photos', $photoFileName);

        // Save video (if provided)
        $videoFileName = null;
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoFileName = $video->getClientOriginalName();
            $video->storeAs('public/videos', $videoFileName);
        }

        // Create a new Photograph instance
        $photograph = Photograph::create([
            'type' => $request->input('type'),
            'name' => $request->input('name'),
            'photo' => $photoFileName,
            'time' => $request->input('time'),
            'longitude' => $request->input('longitude'),
            'latitude' => $request->input('latitude'),
            'video' => $videoFileName,
            // Add other fields as needed
        ]);

        return response()->json(['photograph' => $photograph], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $photograph = Photograph::findOrFail($id);
        return response()->json(['photograph' => $photograph]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function count()
    {
        $count = Photograph::count();

        return response()->json(['count' => $count]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $photograph = Photograph::findOrFail($id);

        // $request->validate([
        //     'type' => 'required|max:20',
        //     'name' => 'required|max:20',
        //     'time' => 'required|date_format:H:i:s',
        //     'longitude' => 'required|numeric',
        //     'latitude' => 'required|numeric',
        //     'photo' => 'sometimes|required|max:255|image', // Add image validation if 'photo' is an image field
        //     'video' => 'sometimes|max:255', // Add validation for video if needed
        //     // Add validation for other fields
        // ]);

        // Update photo if provided
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoFileName = $photo->getClientOriginalName();
            $photo->storeAs('public/photos', $photoFileName);
            $photograph->photo = $photoFileName;
        }

        // Update video if provided
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoFileName = $video->getClientOriginalName();
            $video->storeAs('public/videos', $videoFileName);
            $photograph->video = $videoFileName;
        }

        // Update other fields
        $photograph->type = $request->input('type');
        $photograph->name = $request->input('name');
        $photograph->time = $request->input('time');
        $photograph->longitude = $request->input('longitude');
        $photograph->latitude = $request->input('latitude');

        // Save changes to the database
        $photograph->save();

        return response()->json(['photograph' => $photograph]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $photograph = Photograph::findOrFail($id);
        $photograph->delete();
        return response()->json('1', 204);
    }
}
