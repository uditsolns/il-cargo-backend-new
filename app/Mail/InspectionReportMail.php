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

class InspectionReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, BuildsInspectionReportData;

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
