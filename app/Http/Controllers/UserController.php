<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeUserMail;
use App\Models\ApiUsageLog;
use App\Models\CargoDetail;
use App\Models\Checklist;
use App\Models\ChecklistMaster;
use App\Models\Group;
use App\Models\PhaseZone;
use App\Models\Photograph;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role == 'Channel Partner') {
            // Load users with zones and phases for the Channel Partner
            $users = User::where('channel_partner_id', $user->id)->with('zones', 'phases')->latest()->get();
            return response()->json(['users' => $users]);
        }

        // Load all users with their zones and phases
        $users = User::with('group', 'channel', 'zones', 'phases')->latest()->get();
        return response()->json(['users' => $users]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            // Add validation for other fields
        ]);

        $user = User::create($request->all());

        Mail::to($user->email)->send(new WelcomeUserMail($user));

        return response()->json(['user' => $user], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::with('group')->findOrFail($id);
        $phaseZones = PhaseZone::where('user_id', $user->id)->with('phase', 'zone')->get();
        $response = [
            'user' => $user,
            'phase_zones' => $phaseZones,
            // 'token' => $token
        ];

        return response($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Define the fields that can be updated
        $fillableFields = ['email', 'name', 'phone', 'role', 'group_id'];

        // Loop through the fillable fields and update them if they exist in the request
        foreach ($fillableFields as $field) {
            if ($request->filled($field)) {
                // Update the field with the corresponding value from the request
                $user->$field = $request->input($field);
            }
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        // Save the user
        $user->save();

        // Update the phasezones table if zones are provided
        if ($request->filled('zones')) {
            // First, delete existing phase zones for the user to avoid duplicates
            PhaseZone::where('user_id', $user->id)->delete();

            // Loop through each zone in the request
            foreach ($request->input('zones') as $zone) {
                // Check if zone_id is provided
                if (isset($zone['zone_id']) && $request->filled('phase_id')) {
                    // Create a new phasezone entry for each zone_id
                    PhaseZone::create([
                        'user_id' => $user->id,
                        'zone_id' => $zone['zone_id'],
                        'phase_id' => $request->input('phase_id'),
                    ]);
                }
            }
        }

        return response()->json(['user' => $user]);
    }


    public function update_old(Request $request, $id)
    {
        // dd($request->input('zone_id'));
        print_r($request->input('zones'));
        exit;
        $user = User::findOrFail($id);

        // Define the fields that can be updated
        $fillableFields = ['email', 'name', 'phone', 'role', 'group_id', 'zone_id'];

        // Loop through the fillable fields and update them if they exist in the request
        foreach ($fillableFields as $field) {
            if ($request->filled($field)) {
                // Update the field with the corresponding value from the request
                $user->$field = $request->input($field);
            }
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        // Save the user
        $user->save();

        // Update the phasezones table if zone_id is provided
        if ($request->filled('zone_id') && $request->filled('phase_id')) {
            // Find the existing phasezone entry for the user, or create a new one if it doesn't exist
            $phasezone = PhaseZone::updateOrCreate(
                ['user_id' => $user->id],
                ['zone_id' => $request->input('zone_id'), 'phase_id' => $request->input('phase_id')]
            );
        }

        return response()->json(['user' => $user]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }

    public function allcounts()
    {
        // Get the authenticated user
        $user = Auth::user();

        if ($user->is_admin) {
            // User is an admin, show all counts
            $cargoCount = CargoDetail::count();
            $checklistCount = Checklist::count();
            $checklistMasterCount = ChecklistMaster::count();
            $groupCount = Group::count();
            $userCount = User::count();
            $photographCount = Photograph::count();

            return response()->json([
                'cargo' => $cargoCount,
                'checklist' => $checklistCount,
                'checklistMaster' => $checklistMasterCount,
                'group' => $groupCount,
                'user' => $userCount,
                'photograph' => $photographCount,
                'apis' => ApiUsageLog::count(),
            ]);
        } elseif ($user->role == 'Channel Partner') {
            // User is a Channel Partner, show counts related to the Channel Partner
            $userId = $user->id;
            $groupCount = Group::where('channel_partner_id', $userId)->count();
            $userCount = User::where('channel_partner_id', $userId)->count();
            $cargoCount = CargoDetail::where('channel_partner_id', $userId)->count();

            return response()->json([
                'cargo' => $cargoCount,
                'group' => $groupCount,
                'user' => $userCount,
            ]);
        } elseif ($user->role == "Insured's Dispatch Supervisor" || $user->role == "Insured's Representative") {
            if ($user->group_id) {
                $cargoCount = CargoDetail::where('group_id', $user->group_id)->count();
                $checklistMasterCount = ChecklistMaster::where('group_id', $user->group_id)->count();

                return response()->json([
                    'cargo' => $cargoCount,
                    'checklistMaster' => $checklistMasterCount,
                ]);
            } else {
                return response()->json(['error' => 'Group not found'], 404);
            }
        } else {
            // User is not an admin or Channel Partner, show counts related to the authenticated user
            $cargoCount = CargoDetail::where('user_id', $user->id)->count();

            return response()->json([
                'cargo' => $cargoCount,
            ]);
        }
    }


}
