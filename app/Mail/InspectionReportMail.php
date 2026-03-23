<?php

namespace App\Mail;

use App\Models\CargoDetail;
use App\Services\GoogleMapsStaticService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InspectionReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $cargo;
    public $complianceSummary;
    private $googleMapsService;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(CargoDetail $cargo)
    {
        $this->cargo = $cargo->loadMissing(['photographs', 'photographs.zone', 'photographs.phase', 'checklists', 'checklists.phase', 'checklists.zone', 'group']);
        $this->complianceSummary = $this->calculateComplianceSummary();
        $this->googleMapsService = new GoogleMapsStaticService();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->complianceSummary['breachedCount'] > 0
            ? "⚠️ Cargo Inspection Report - Action Required (Dispatch: {$this->cargo->dispatch_id})"
            : "✅ Cargo Inspection Report - All Clear (Dispatch: {$this->cargo->dispatch_id})";

        $mail = $this->subject($subject)
            ->markdown('mail.inspection-report')
            ->with([
                'cargo' => $this->cargo,
                'summary' => $this->complianceSummary
            ]);

        // Generate and attach PDF report
        $pdf = $this->generatePdfReport();
        $mail->attachData($pdf->output(), "inspection-report-{$this->cargo->dispatch_id}.pdf", [
            'mime' => 'application/pdf',
        ]);

        return $mail;
    }

    /**
     * Calculate compliance summary
     */
    private function calculateComplianceSummary()
    {
        $checklists = $this->cargo->checklists ?? [];
        $totalChecks = count($checklists);
        $breachedCount = collect($checklists)->where('is_sop_breached', 'yes')->count();
        $compliantCount = $totalChecks - $breachedCount;
        $complianceRate = $totalChecks > 0 ? round(($compliantCount / $totalChecks) * 100, 1) : 0;

        return [
            'totalChecks' => $totalChecks,
            'breachedCount' => $breachedCount,
            'compliantCount' => $compliantCount,
            'complianceRate' => $complianceRate,
            'status' => $breachedCount === 0 ? 'PASSED' : 'ATTENTION REQUIRED',
            'totalPhotographs' => count($this->cargo->photographs ?? []),
            'zonesInspected' => collect($this->cargo->photographs ?? [])->groupBy('phase.name')->count()
        ];
    }

    /**
     * Process location data for display - simplified version without grouping
     */
    function processLocationData($photographs)
    {
        if (empty($photographs)) {
            return [];
        }


        // Group by zone (which is stored in phase key due to data structure)
        $allLocations = collect($photographs)
            ->groupBy(function ($photo) {
                return $photo['phase']['name'] ?? 'Unknown Zone';
            })
            ->map(function ($zonePhotos) {
                // Sort photos by time before getting the first one
                $sortedPhotos = $zonePhotos->sortBy('created_at');
                $firstPhoto = $sortedPhotos->first();

                $hasCoordinates = !empty($firstPhoto['latitude']) && !empty($firstPhoto['longitude']);

                return [
                    'zone_name' => $firstPhoto['phase']['name'] ?? 'Unknown Zone',
                    'latitude' => $hasCoordinates ? (float)$firstPhoto['latitude'] : null,
                    'longitude' => $hasCoordinates ? (float)$firstPhoto['longitude'] : null,
                    'time' => \Carbon\Carbon::parse($firstPhoto['created_at'])->format('d/m/Y H:i'),
                    'raw_time' => $firstPhoto['created_at'],
                    'photo_count' => $zonePhotos->count(),
                    'has_coordinates' => $hasCoordinates
                ];
            })
            ->sortBy('raw_time')
            ->values();

        if ($allLocations->isEmpty()) {
            return [];
        }

        // Filter only locations with coordinates for map display
        $locationsWithCoordinates = $allLocations->filter(function ($location) {
            return $location['has_coordinates'];
        })->values();

        // Calculate horizontal positions only for locations with coordinates
        $totalLocationsForMap = $locationsWithCoordinates->count();

        $locationsWithCoordinates = $locationsWithCoordinates->map(function ($location, $index) use ($totalLocationsForMap) {
            if ($totalLocationsForMap == 1) {
                $x = 50; // Center position for single location
            } else {
                // Distribute evenly across horizontal space with padding
                $padding = 10; // 10% padding on each side
                $availableWidth = 100 - (2 * $padding);
                $x = $padding + ($index * $availableWidth / ($totalLocationsForMap - 1));
            }

            return array_merge($location, [
                'x' => $x,
                'y' => 40 // Fixed vertical center position
            ]);
        })->toArray();

        // Return both: all locations for table, locations with coordinates for map
        return [
            'all' => $allLocations->toArray(),
            'map' => $locationsWithCoordinates
        ];
    }

    /**
     * Generate Google Maps static image URL - simplified version
     */
    private function generateMapImageUrl($locationData)
    {
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

            $bestFitOptions = $this->googleMapsService->calculateBestFit($locations);

            // Use satellite map with simple red markers
            $options = [
                'size' => '800x400',
                'maptype' => 'roadmap',
                'marker_color' => 'red',
                'marker_size' => 'mid',
                'center' => $bestFitOptions['center'],
                'zoom' => $bestFitOptions['zoom'],
            ];

            return $this->googleMapsService->generateMapWithMarkers($locations, $options);

        } catch (\Exception $e) {
            // Log error and return null to fall back to no map
            \Log::error('Failed to generate Google Maps image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate PDF report
     */
    public function generatePdfReport()
    {
        $locationData = $this->processLocationData($this->cargo->photographs);

        $data = [
            'cargo' => $this->cargo->toArray(),
            'summary' => $this->complianceSummary,
            'allLocations' => $locationData['all'] ?? [],  // For table display
            'mapLocations' => $locationData['map'] ?? [],  // For map display
            'mapImageUrl' => $this->generateMapImageUrl($locationData['map'] ?? [])
        ];

        return Pdf::loadView('pdf.inspection-report', $data)
            ->setPaper('a4')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
    }
}
