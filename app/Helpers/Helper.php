<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\CargoDetail;

class Helper
{
    /**
     * Generate a dispatch ID.
     *
     * @param string $userInitials The initials of the user.
     * @param int $increment The increment value.
     * @return string The generated dispatch ID.
     */
        static   function generateDispatchId()
    {
        // Get the initials of the logged-in user
        $user = Auth::user();
        $initials = strtoupper(substr($user->name, 0, 2));

        // Get the current date in the format DDMMYY
        $currentDate = Carbon::now()->format('dmy');

        // Get the latest dispatch ID
        $latestDispatchId = CargoDetail::where('user_id', $user->id)->max('dispatch_id');

        // Extract the numeric part and increment it
        $numericPart = $latestDispatchId ? (int)substr($latestDispatchId, 2, 3) + 1 : 1;
        $numericPartPadded = str_pad($numericPart, 3, '0', STR_PAD_LEFT);

        // Construct the dispatch ID
        $dispatchId = $initials . $numericPartPadded . $currentDate;

        return $dispatchId;
    }
}
