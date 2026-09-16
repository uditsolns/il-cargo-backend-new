<?php

namespace App\Mail;

use App\Mail\Concerns\BuildsInspectionReportData;
use App\Models\CargoDetail;
use App\Services\GoogleMapsStaticService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * The trip-completion email (sent once, when pending_servey flips to 1 -
 * see CargoCompleted). Same PDF-generation shape as InspectionReportMail
 * (shares its compliance-summary/location-map logic via
 * BuildsInspectionReportData), plus the shipment route map and FASTag/
 * container tracking data InspectionReportMail doesn't have - it's an
 * on-demand/in-progress report, this is the final one.
 */
class FinalReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, BuildsInspectionReportData;

    public $cargo;
    public $complianceSummary;
    private $googleMapsService;

    public function __construct(CargoDetail $cargo)
    {
        $this->cargo = $cargo->loadMissing([
            'photographs',
            'photographs.zone',
            'photographs.phase',
            'checklists',
            'checklists.phase',
            'checklists.zone',
            'group',
            'fastagTransactions',
            'containerTrackingEvents',
        ]);
        $this->complianceSummary = $this->calculateComplianceSummary();
        $this->googleMapsService = new GoogleMapsStaticService();
    }

    public function build()
    {
        $mail = $this->subject("Final Dispatch Report - {$this->cargo->dispatch_id} (Trip Completed)")
            ->markdown('mail.final-report')
            ->with([
                'cargo' => $this->cargo,
                'summary' => $this->complianceSummary,
            ]);

        $pdf = $this->generatePdfReport();
        $mail->attachData($pdf->output(), "final-report-{$this->cargo->dispatch_id}.pdf", [
            'mime' => 'application/pdf',
        ]);

        return $mail;
    }

    public function generatePdfReport()
    {
        $locationData = $this->processLocationData($this->cargo->photographs);

        $data = [
            'cargo' => $this->cargo->toArray(),
            'summary' => $this->complianceSummary,
            'mapLocations' => $locationData['map'] ?? [],
            'mapImageUrl' => $this->generateMapImageUrl($locationData['map'] ?? []),
            'routeMapUrl' => $this->generateRouteMapUrl(),
            'originLat' => $this->cargo->dispatch_lat,
            'originLng' => $this->cargo->dispatch_long,
            'destinationLat' => $this->cargo->destination_lat,
            'destinationLng' => $this->cargo->destination_long,
            'fastagTransactions' => $this->cargo->fastagTransactions
                ->map(fn($t) => ['fetched_at' => $t->fetched_at, 'payload' => $t->payload])
                ->all(),
            'containerTrackingEvents' => $this->cargo->containerTrackingEvents
                ->map(fn($e) => ['fetched_at' => $e->fetched_at, 'payload' => $e->payload])
                ->all(),
        ];

        return Pdf::loadView('pdf.final-report', $data)
            ->setPaper('a4')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
    }

    /**
     * Straight origin->destination line (see
     * GoogleMapsStaticService::generateOriginDestinationMap) - null when
     * either endpoint's coordinates weren't recorded, so the view can show
     * a graceful "unavailable" message instead of a broken image.
     */
    private function generateRouteMapUrl(): ?string
    {
        // Legacy rows often store the literal string "null" rather than a
        // real NULL in these varchar columns - is_numeric() rejects that
        // (unlike a truthiness check, which treats "null" as a truthy string).
        if (!is_numeric($this->cargo->dispatch_lat) || !is_numeric($this->cargo->dispatch_long)
            || !is_numeric($this->cargo->destination_lat) || !is_numeric($this->cargo->destination_long)) {
            return null;
        }

        try {
            return $this->googleMapsService->generateOriginDestinationMap(
                ['lat' => (float) $this->cargo->dispatch_lat, 'lng' => (float) $this->cargo->dispatch_long],
                ['lat' => (float) $this->cargo->destination_lat, 'lng' => (float) $this->cargo->destination_long],
            );
        } catch (\Exception $e) {
            \Log::error('Failed to generate route map: ' . $e->getMessage());
            return null;
        }
    }
}
