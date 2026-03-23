<?php

namespace App\Http\Controllers;

use App\Services\APIClub;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VerificationController extends Controller
{
    public function verifyRC(Request $request) {
        // Validate the input first
        $request->validate([
            'vehicleId' => ['required', 'string', 'max:11']
        ]);

        // Send request to API
        return APIClub::sendRequest(APIClub::RC,
            ['vehicleId' => $request->input('vehicleId')]);
    }

    public function sendAadharOTP(Request $request) {
        $request->validate([
            'aadhaar_no' => ['required', 'string', 'size:12'] // Aadhaar should be exactly 12 digits
        ]);

        // Send POST request to the API to send OTP
        return APIClub::sendRequest(APIClub::SEND_OTP, [
            'aadhaar_no' => $request->input("aadhaar_no"),
        ]);
    }

    public function verifyAadharOTP(Request $request) {
        // Validate the OTP input
        $request->validate([
            'ref_id' => ["required", "string"],
            'otp' => ['required', 'size:6'],
        ]);

        // Send OTP verification request
        return APIClub::sendRequest(APIClub::SUBMIT_OTP, [
            'ref_id' => $request->input("ref_id"),
            'otp' => $request->input('otp'),
        ]);
    }

    public function verifyDL(Request $request) {
        // Validate the input Driving License number
        $request->validate([
            'dl_no' => ['required', 'string', 'min:10'],
            'dob' => ['required', 'date'],
        ]);

        // Send request to API
        return APIClub::sendRequest(APIClub::DL, [
            'dl_no' => $request->input('dl_no'),
            'dob' => Carbon::parse(($request->input("dob")))->format("d-m-Y")
        ]);
    }
}
