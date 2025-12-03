<?php

namespace App\Http\Controllers;

use App\Models\PhaseZone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string',
            'phone' => 'required|string|unique:users,phone',
            // 'role' => 'required',
            // 'group_id' => 'required',
            'password_confirmation' => 'required|same:password'
        ]);

        if ($fields->fails()) {
            return response()->json(['error' => $fields->errors()], 403);
        }

        $user = new User();
        $user->role = $request->role;
        $user->group_id = $request->group_id;
        $user->user_status = $request->user_status;
        $user->channel_partner_id = $request->channel_partner_id;
        $user->fill($request->except('password_confirmation'));
        $user->password = bcrypt($request->password);

        $user->save();

        // Associate zones with the user
        if ($request->zones) {
            foreach ($request->zones as $zone) {
                $zoneId = $zone['zone_id'];
                // Create a new PhaseZone entry
                PhaseZone::create([
                    'user_id' => $user->id,
                    'phase_id' => $request->phase_id,
                    'zone_id' => $zoneId,
                    // Add other necessary fields here
                ]);
            }
        }

        // Fetch PhaseZone data associated with the user
        $phaseZones = PhaseZone::where('user_id', $user->id)->with('phase', 'zone')->get();

        $token = $user->createToken('myapptoken')->plainTextToken;

        $user = User::find($user->id);

        $response = [
            'user' => $user,
            'phase_zone' => $phaseZones, // Include the phase zones data in the response
            'token' => $token
        ];

        return response($response, 201);

    }


    public function login(Request $request)
    {
        $fields = Validator::make($request->all(), [
            // 'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($fields->fails()) {
            return response()->json(['error' => $fields->errors()], 403);
        }

        // Check email or phone
        $user = null;
        if ($request->has('email')) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->has('phone')) {
            $user = User::where('phone', $request->phone)->first();
        } else {
            return response([
                'message' => 'Bad creds'
            ], 401);
        }

        // Check password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'Bad creds'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;
        $phaseZones = PhaseZone::where('user_id', $user->id)->with('phase', 'zone')->get();
        $response = [
            'user' => $user,
            'phase_zones' => $phaseZones,
            'token' => $token
        ];

        return response($response, 201);
    }


    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Send password reset link to user's email
        Password::sendResetLink($request->only('email'));

        return response()->json([
            "message" => "Password reset link has been sent to the provided email if it exists."
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // Optional: log the user out of other devices
                $user->tokens()->delete();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been reset successfully.']);
        }

        return response()->json(['message' => __($status)], 400);
    }

    public function updatePassword(Request $request)
    {
        $fields = Validator::make($request->all(), [
            'token' => 'required|string',
            'password' => 'required|string',
            'confirmation_password' => 'required|string|same:password'
        ]);

        if ($fields->fails()) {
            return response()->json(['error' => $fields->errors()], 422);
        }

        $user = User::where('forget_password_token', $request->token)->first();

        // if ($user->is_password_changed == 1) {
        //     return response()->json($arrayName = array('error' => 'your password is already been changed.'));
        // }

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json($arrayName = array('success' => 'password changed'));
        }

        return response()->json($arrayName = array('error' => 'user does not exists or token expired'));
    }

    public function googleLogin(Request $request)
    {

        $oldUser = User::where('email', $request->email)->first();
        if (isset($oldUser)) {

            $token = $oldUser->createToken('myapptoken')->plainTextToken;

            $response = [
                'user' => $oldUser,
                'token' => $token
            ];

            return response($response, 201);
        } else {

            $fields = Validator::make($request->all(), [
                'email' => 'required|string|email|unique:users,email'
            ]);

            if ($fields->fails()) {
                $response = [
                    'status_message' => $fields->errors()->first()
                ];
                return response()->json($response);
            }

            $user = User::create([
                'email' => $request->email,
                'password' => bcrypt('password'),
                'is_google_login' => 1
            ]);

            $token = $user->createToken('myapptoken')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            return response($response, 201);
        }
    }
}
