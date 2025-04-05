<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\CargoDetail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PendingSurveyMail;
use App\Models\Email;
use App\Models\Group;

class CargoCompleted implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
{
    // Retrieve the latest cargo detail with pending_survey equal to 1
$cargoDetail = CargoDetail::where('pending_servey', 1)
->latest() // Order by the created_at column in descending order
->first(); // Retrieve only the latest cargo detail

if ($cargoDetail) {
$userId = $cargoDetail->user_id;
$user = User::find($userId);
$groupId = $user->group_id;

// Get emails associated with the group
$emails = Group::where('id', $groupId)->pluck('additional_emails')->first();
$emails = json_decode($emails, true); // Decode JSON as associative array
if ($emails) {
    // Send email to each recipient
    foreach ($emails as $email) {
        Log::info("emails data : " . print_r($email, true)); 
        Mail::to($email)->send(new PendingSurveyMail($user));
        Log::info("Mail sent to email: $email");
    }
    
    Log::info("Mails sent to all recipients in the group");
} else {
    Log::info("No additional emails found for the group");
}
} else {
Log::info("No cargo details found with pending survey equal to 1");
}

}

}
