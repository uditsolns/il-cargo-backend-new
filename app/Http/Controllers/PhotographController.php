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
    // If the user is an admin, fetch all photographs
    $photographs = Photograph::with('zone', 'phase', 'zone.phase')->latest()->get();
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
      $type = $request->input('type');
    $name = $request->input('name');
    $cargoId = $request->input('cargo_id');
    $phaseId = $request->input('phase_id');
    
    \Log::info("Palette No: ". $request->input("palette_no"));

    // Check if a photograph with the same type, name, and cargo_id exists
    $existingPhotograph = Photograph::where('type', $type)
                                     ->where('name', $name)
                                     ->where('cargo_id', $cargoId)
                                     ->where('palette_no', $request->input('palette_no'))
                                     ->first();
// dd($existingPhotograph);
    if ($existingPhotograph) {
        // Update photo if provided
        $photoFileName = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoFileName = $photo->getClientOriginalName();
            $photo->storeAs('public/photos', $photoFileName);
            $existingPhotograph->photo = $photoFileName;
        }

        // Update video if provided
        $videoFileName = null;
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoFileName = $video->getClientOriginalName();
            $video->storeAs('public/videos', $videoFileName);
            $existingPhotograph->video = $videoFileName;
        }

        // Update other fields
        $existingPhotograph->time = $request->input('time');
        $existingPhotograph->longitude = $request->input('longitude');
        $existingPhotograph->latitude = $request->input('latitude');

        // Save changes to the database
        $existingPhotograph->save();

        return response()->json(['photograph' => $existingPhotograph]);
    } else {
        // Create a new Photograph instance
        $photoFileName = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoFileName = $photo->getClientOriginalName();
            $photo->storeAs('public/photos', $photoFileName);
        }

        // Save video if provided
        $videoFileName = null;
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $videoFileName = $video->getClientOriginalName();
            $video->storeAs('public/videos', $videoFileName);
        }

        $photograph = Photograph::create([
            'type' => $type,
            'name' => $name,
            'photo' => $photoFileName,
            'time' => $request->input('time'),
            'zone_id' => $request->input('zone_id'),
            'longitude' => $request->input('longitude'),
            'latitude' => $request->input('latitude'),
            'reason' => $request->input('reason'),
            'phase_id' => $request->input('phase_id'),
            'cargo_id' => $cargoId,
            'video' => $videoFileName,
            'palette_no' => $request->input('palette_no'),
            // Add other fields as needed
        ]);

        return response()->json(['photograph' => $photograph], 201);
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $photograph = Photograph::where('id',$id)->with('zone', 'phase', 'zone.phase')->first();
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
     // Find the photograph by ID
    $photograph = Photograph::findOrFail($id);
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
        dd($request->input('type'));
    // Check if the type is already there, then update it
    if ($existingPhotograph = Photograph::where('type', $request->input('type'))->first()) 
    {
        // Update other fields
        $existingPhotograph->name = $request->input('name');
        $existingPhotograph->time = $request->input('time');
        $existingPhotograph->longitude = $request->input('longitude');
        $existingPhotograph->latitude = $request->input('latitude');
        $existingPhotograph->palette_no = $request->input('palette_no');
        $existingPhotograph->save();
        
        return response()->json(['photograph' => $existingPhotograph]);
    }

    // If type is not found, update the existing photograph
    $photograph->type = $request->input('type');
    $photograph->name = $request->input('name');
    $photograph->time = $request->input('time');
    $photograph->longitude = $request->input('longitude');
    $photograph->latitude = $request->input('latitude');
    $photograph->phase_id = $request->input('phase_id');
    $photograph->palette_no = $request->input('palette_no');

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
