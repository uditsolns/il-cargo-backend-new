<?php

use App\Models\CargoDetail;
use App\Services\GoogleMapsStaticService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Process location data for map visualization
 */
function processLocationData($photographs)
{
    if (empty($photographs)) {
        return [];
    }

    // Group by zone (which is stored in phase key due to data structure)
    $locationsByZone = collect($photographs)
        ->filter(function ($photo) {
            return !empty($photo['latitude']) && !empty($photo['longitude']);
        })
        ->groupBy(function ($photo) {
            return $photo['phase']['name'] ?? 'Unknown Zone';
        })
        ->map(function ($zonePhotos) {
            // Get the first photo from each zone for location
            $firstPhoto = $zonePhotos->first();
            return [
                'zone_name' => $firstPhoto['phase']['name'] ?? 'Unknown Zone',
                'latitude' => (float)$firstPhoto['latitude'],
                'longitude' => (float)$firstPhoto['longitude'],
                'time' => \Carbon\Carbon::parse($firstPhoto['created_at'])->format('d/m/Y H:i'),
                'raw_time' => $firstPhoto['created_at'],
                'photo_count' => $zonePhotos->count() // Add photo count
            ];
        })
        ->sortBy('raw_time')
        ->values();

    if ($locationsByZone->isEmpty()) {
        return [];
    }

    // Calculate horizontal positions for timeline layout
    $totalLocations = $locationsByZone->count();

    return $locationsByZone->map(function ($location, $index) use ($totalLocations) {
        if ($totalLocations == 1) {
            $x = 50; // Center position for single location
        } else {
            // Distribute evenly across horizontal space with padding
            $padding = 10; // 10% padding on each side
            $availableWidth = 100 - (2 * $padding);
            $x = $padding + ($index * $availableWidth / ($totalLocations - 1));
        }

        return array_merge($location, [
            'x' => $x,
            'y' => 40 // Fixed vertical center position
        ]);
    })->toArray();
}

function generateMapImageUrl($locationData)
{
    $googleMapsService = app(GoogleMapsStaticService::class);

    if (empty($locationData)) {
        return null;
    }

    try {
        // Convert location data to simple lat/lng format
        $locations = array_map(function ($location) {
            return [
                'lat' => $location['latitude'],
                'lng' => $location['longitude']
            ];
        }, $locationData);

        $bestFitOptions = $googleMapsService->calculateBestFit($locations);

        // Use satellite map with simple red markers
        $options = [
            'size' => '800x400',
            'maptype' => 'roadmap',
            'marker_color' => 'red',
            'marker_size' => 'mid',
            'center' => $bestFitOptions['center'],
            'zoom' => $bestFitOptions['zoom'],
        ];

        return $googleMapsService->generateMapWithMarkers($locations, $options);

    } catch (\Exception $e) {
        // Log error and return null to fall back to no map
        \Log::error('Failed to generate Google Maps image: ' . $e->getMessage());
        return null;
    }
}

Route::get('/report', function () {
    $cargoDetail = CargoDetail::with(['group', 'checklists', 'photographs.phase', 'photographs.zone'])->find(193);

//    Mail::to('web.tarachand@gmail.com')->send(new InspectionReportMail($cargoDetail));

//    return "mail sent";

    $checklists = $cargoDetail->checklists ?? [];
    $totalChecks = count($checklists);
    $breachedCount = collect($checklists)->where('is_sop_breached', 'yes')->count();
    $compliantCount = $totalChecks - $breachedCount;
    $complianceRate = $totalChecks > 0 ? round(($compliantCount / $totalChecks) * 100, 1) : 0;

    $photographs = $cargoDetail->photographs ?? [];

    $complianceSummary = [
        'totalChecks' => $totalChecks,
        'breachedCount' => $breachedCount,
        'compliantCount' => $compliantCount,
        'complianceRate' => $complianceRate,
        'status' => $breachedCount === 0 ? 'PASSED' : 'ATTENTION REQUIRED',
        'totalPhotographs' => count($photographs),
        'zonesInspected' => collect($photographs)->groupBy('phase.name')->count()
    ];

    $locationData = processLocationData($photographs);

    $data = [
        'cargo' => $cargoDetail->toArray(),
        'summary' => $complianceSummary,
        'locationData' => $locationData,
        'mapImageUrl' => generateMapImageUrl($locationData),
    ];

//    return view('pdf.inspection-report', $data);

    return Pdf::loadView('pdf.inspection-report', $data)
        ->setPaper('a4')
        ->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
        ])->stream();
});

Route::get('/send-email', function () {
    $details = [
        'title' => 'Test Email from Laravel using ZeptoMail',
        'body' => 'This is a test email sent using ZeptoMail.'
    ];

    Mail::to('web.tarachand@gmail.com')->send(new \App\Mail\TestMail($details));

    return 'Email has been sent successfully!';
});

Route::get("/google-map", function (Request $request) {
    $locations = [
        ['lat' => 19.076090, 'lng' => 72.877426], // Mumbai Central (general city center)
//        ['lat' => 19.228825, 'lng' => 72.854118], // Borivali
//        ['lat' => 19.043240, 'lng' => 72.865410], // Chembur
    ];

    $mapsService = app(GoogleMapsStaticService::class);

//    $mapUrl = $mapsService->generateMapWithMarkers($locations, [
//        'size' => '800x600',
//        'marker_color' => 'blue',
//        'marker_size' => 'mid'
//    ]);

    $bestFit = $mapsService->calculateBestFit($locations);
    $mapUrl = $mapsService->generateMapWithMarkers($locations, [
        'center' => $bestFit['center'],
        'zoom' => $bestFit['zoom']
    ]);

    return $mapUrl;
});


//
//Route::get('forgot-password', function () {
//    $user = User::first();
//    $token = Str::random(64);
//
//    $resetUrl = url("/v1/password/reset/{$token}");
//
//    Mail::to($user->email)->send(new ForgotPasswordMail($token, $user));
//
//    return view('emails.forgot-password')
//        ->with([
//            'resetUrl' => $resetUrl,
//            'user' => $user,
//            'token' => $token,
//        ]);
//});
//
//Route::get('welcome-user', function () {
//    $user = User::first();
//    $loginUrl = url('/login');
//
//
//    Mail::to($user->email)->send(new WelcomeUserMail($user));
//
//    return view('emails.welcome-user')
//        ->with([
//            'loginUrl' => $loginUrl,
//            'user' => $user,
//        ]);
//});
