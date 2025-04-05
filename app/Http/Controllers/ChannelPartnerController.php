<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChannelPartner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ChannelPartnerController extends Controller
{

//     public function update(Request $request, $id)
// {
//     // Retrieve the user based on the ID
//     $channelPartner = ChannelPartner::findOrFail($id);

//     // Update user information
//     $channelPartner->name = $request->input('name');
//     $channelPartner->email = $request->input('email');
//     $channelPartner->phone = $request->input('phone');
//     $channelPartner->password = bcrypt($request->input('password'));

//     // Save the updated user
//     $channelPartner->save();

//     // Generate a new token
//     $token = $channelPartner->createToken('myapptoken')->plainTextToken;

//     // Return the updated user and token in the response
//     return response()->json(['user' => $channelPartner, 'token' => $token], 200);
// }

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
        $channelPartner = null;
        if ($request->has('email')) {
            $channelPartner = ChannelPartner::where('email', $request->email)->first();
        } elseif ($request->has('phone')) {
            $channelPartner = ChannelPartner::where('phone', $request->phone)->first();
        } else {
            return response([
                'message' => 'Bad creds'
            ], 401);
        }
    
        // Check password
        if (!$channelPartner || !Hash::check($request->password, $channelPartner->password)) {
            return response([
                'message' => 'Bad creds'
            ], 401);
        }
    
        $token = $channelPartner->createToken('myapptoken')->plainTextToken;
        // dd($token);
        $response = [
            'user' => $channelPartner,
            'token' => $token
        ];
    
        return response($response, 201);
    }
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
        $role = 'Channel Partner';
        $user = new ChannelPartner();
        $user->role = $role;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->fill($request->except('password_confirmation'));
        $user->password = bcrypt($request->password);
    
        $user->save();
    
        $token = $user->createToken('myapptoken')->plainTextToken;
    
        $user = ChannelPartner::find($user->id);
    
        $response = [
            'user' => $user,
            'token' => $token
        ];
    
        return response($response, 201);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role == 'Channel Partner') {
           return ChannelPartner::where('id', $user->id)->latest()->get();
        }
        return ChannelPartner::latest()->get();
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

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
public function update(Request $request, $id)
{
    $user = ChannelPartner::findOrFail($id);

    // Define the fields that can be updated
    $fillableFields = ['email', 'name', 'phone'];

    // Loop through the fillable fields and update them if they exist in the request and are not null
    foreach ($fillableFields as $field) {
        if ($request->filled($field) && $request->input($field) !== null) {
            // Update the field with the corresponding value from the request
            $user->$field = $request->input($field);
        }
    }

    // Update password if provided and not null
    if ($request->filled('password') && $request->input('password') !== null) {
        $user->password = bcrypt($request->input('password'));
    }

    $user->save();

    return response()->json(['user' => $user]);
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
