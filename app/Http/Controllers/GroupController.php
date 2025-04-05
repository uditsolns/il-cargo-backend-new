<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = auth()->user();
        if (!$userId) {
        return response()->json(['error' => 'Unauthorized'], 401);
        }

        if($userId->role == "Channel Partner"){  
        // dd($userId->role);
       $groups = Group::where('channel_partner_id', $userId->id)->latest()->orderByDesc('id')->get();
            return response()->json(['groups' => $groups]);
        }
        $groups = Group::latest()->orderByDesc('id')->get();
        return response()->json(['groups' => $groups]);
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
        $user = auth()->user();
        if ($user->role == 'Channel Partner') {
            $channel_partner_id = $user->id;
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'gst' => 'required',
            // Add validation for other fields
        ]);
        if ($request->hasFile('photo')) {
    $photo = $request->file('photo');
    $photoFileName = $photo->getClientOriginalName();
    $photo->storeAs('public/groups', $photoFileName);
}   
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 403);
        }
        $additionalEmails = $request->input('additional_emails'); null;
        $group = Group::create([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'gst' => $request->input('gst'),
            'photo' => isset($photoFileName) ? $photoFileName : null,
            'additional_emails' => $additionalEmails,
            // 'channel_partner_id' => isset($channel_partner_id) ? $channel_partner_id : null,
        ]);
        // if ($request->emails) {
        //     foreach ($request->emails as $email) {
        //         // Find the group by its ID and update its additional_emails field
        //         Group::where('id', $group->id)->update(['additional_emails' => $email]);
        //     }
        // }
        
        return response()->json(['group' => $group], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = Group::findOrFail($id);
        return response()->json(['group' => $group]);
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
        $count = Group::count();

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
    // Find the group or fail if not found
    $group = Group::findOrFail($id);

    // Check if a file is present in the request
    $photoFileName = null;
    if ($request->hasFile('photo')) {
        // Process file upload
        $photo = $request->file('photo');
        $photoFileName = $photo->getClientOriginalName();
        $photo->storeAs('public/groups', $photoFileName);
    }

    // Get additional emails from the request
    $additionalEmails = $request->input('additional_emails');

    // Update group information
    $updateData = [
        'name' => $request->input('name'),
        'address' => $request->input('address'),
        'city' => $request->input('city'),
        'gst' => $request->input('gst'),
        'additional_emails' => $additionalEmails,
    ];

    // Only set 'photo' if $photoFileName is not null
    if ($photoFileName) {
        $updateData['photo'] = $photoFileName;
    }

    // Update the group with the new data
    $group->update($updateData);

    // Return the updated group as a JSON response
    return response()->json($group);
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();
        return response()->json(1, 204);
    }
}
