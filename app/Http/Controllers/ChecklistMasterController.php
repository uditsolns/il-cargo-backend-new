<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChecklistMaster;
use App\Models\User;
use App\Models\Group;
use App\Models\ChannelPartner;
use Illuminate\Support\Facades\Validator;

class ChecklistMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $authUser = auth()->user();
// Check if the authenticated user is an admin
if ($authUser->is_admin == 1) {
    // If the user is an admin, fetch all checklist masters
    $checklistMasters = ChecklistMaster::with('group')->latest()->get();
} 
elseif ($authUser->role == 'Channel Partner' || $authUser->user_status == 1) {
    // If the user is a Channel Partner, check if they have a channel_partner_id
    $channelPartnerId = $authUser->channel_partner_id;

    if ($channelPartnerId) {
        // Fetch the Channel Partner data using the channel_partner_id
        $channelPartner = ChannelPartner::find($channelPartnerId);

        // If the Channel Partner exists, retrieve all groups associated with that Channel Partner
        if ($channelPartner) {
            $groups = Group::where('channel_partner_id', $channelPartnerId)->pluck('id');

            // Fetch all checklist masters associated with these groups, along with group and channel_partner data
            $checklistMasters = ChecklistMaster::whereIn('group_id', $groups)
                ->with(['group', 'group.channelPartner'])
                ->latest()
                ->get();
        } else {
            // If the Channel Partner does not exist, return an appropriate response
            $checklistMasters = [];
        }
    } else {
        // If the channel_partner_id is not found, return an empty array or any other appropriate response
        $checklistMasters = [];
    }
}

else {
    // For regular users, fetch only the checklist masters associated with the user's group
    $groupId = $authUser->group_id;
    $checklistMasters = ChecklistMaster::where('group_id', $groupId)->with('group')->latest()->get();
}

// Return the checklist masters as a JSON response
return response()->json(['checklist_masters' => $checklistMasters]);


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
            'question' => 'required',
            'instruction' => 'required',
            'group_id' => 'required|exists:groups,id',
            // 'answer' => 'required|max:20',
            // Add validation for other fields
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 403);
        }
        $requestData = $request->all();
    $requestData['group_id'] = auth()->user()->group_id;

    // $checklistMaster = ChecklistMaster::create($requestData);
        $checklistMaster = ChecklistMaster::create($request->all());
        return response()->json(['checklist_master' => $checklistMaster], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $checklistMaster = ChecklistMaster::findOrFail($id);
        return response()->json(['checklist_master' => $checklistMaster]);
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
        $count = ChecklistMaster::count();

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
        $checklistMaster = ChecklistMaster::findOrFail($id);
        $checklistMaster->update($request->all());
        return response()->json(['checklist_master' => $checklistMaster]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $checklistMaster = ChecklistMaster::findOrFail($id);
        $checklistMaster->delete();
        return response()->json(1, 204);
    }
}
