{{-- resources/views/mail/final-report.blade.php --}}
{{-- Cloned from mail/inspection-report.blade.php for the trip-completion
     email (sent once, via CargoCompleted / FinalReportMail). --}}
    <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Final Dispatch Report</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, sans-serif; color:#333;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
       style="background-color:#f4f6f8; padding:30px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="650" cellspacing="0" cellpadding="0" border="0"
                   style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.05);">
                <!-- HEADER -->
                <tr>
                    <td style="background:#007bff; padding:20px; text-align:center;">
                        <img src="http://ilcargocare.com/ilcargo-backend-new/storage/app/public/glob-3600-logo.jpg" alt="Globe 360 Cloud Logistic"
                             style="max-width:100px; height:auto; margin-bottom:15px; display:block; margin-left:auto; margin-right:auto;">
                        <h1 style="margin:0; font-size:20px; color:#ffffff;">Final Dispatch Report</h1>
                    </td>
                </tr>

                <!-- BODY CONTENT -->
                <tr>
                    <td style="padding:30px;">
                        <p style="margin-top:0;">Dear <strong>{{ $cargo->group->name ?? 'Valued Customer' }}</strong>,
                        </p>

                        <p>Dispatch <strong>{{ $cargo->dispatch_id }}</strong> has been completed. Please find the
                            final report attached, including the shipment route, SOP inspection results, and any
                            FASTag/container tracking data collected for this trip.</p>

                        @if($summary['breachedCount'] > 0)
                            <div
                                style="border-left:5px solid #ffc107; background:#fff8e1; padding:15px; margin:20px 0; border-radius:4px;">
                                <h3 style="margin-top:0; color:#856404;">⚠️ Attention Required</h3>
                                <p><strong>{{ $summary['breachedCount'] }}</strong> out of
                                    <strong>{{ $summary['totalChecks'] }}</strong> inspection criteria were
                                    breached as per the self-declaration entries of Insured's representative.</p>
                                <p><strong>Compliance Rate:</strong> {{ $summary['complianceRate'] }}%</p>
                            </div>
                        @else
                            <div
                                style="border-left:5px solid #28a745; background:#e9f7ef; padding:15px; margin:20px 0; border-radius:4px;">
                                <h3 style="margin-top:0; color:#155724;">✅ All Checks Passed</h3>
                                <p>All <strong>{{ $summary['totalChecks'] }}</strong> Inspection Criteria were
                                    complied as per the self-declaration entries of Insured's representative.</p>
                                <p><strong>Compliance Rate:</strong> {{ $summary['complianceRate'] }}%</p>
                            </div>
                        @endif

                        <!-- Dispatch Summary -->
                        <h2 style="font-size:18px; border-bottom:2px solid #eee; padding-bottom:5px;">📦 Dispatch
                            Summary</h2>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="border-collapse:collapse; margin:15px 0; margin-bottom: 40px">
                            <tbody>
                            @php
                                $dispatchDetails = [
                                    'Dispatch ID' => $cargo->dispatch_id,
                                    'Vehicle Registration' => $cargo->veh_reg_no,
                                    'Cargo Serial' => $cargo->cargo_unit_serial_no,
                                    'Invoice Value' => '₹'.number_format(floatval($cargo->invoice_value)),
                                    'Completed' => \Carbon\Carbon::parse($cargo->updated_at)->format('d/m/Y, h:i A'),
                                    'Destination' => $cargo->address . " (PIN: " . $cargo->destination_pin . ")"
                                ];
                            @endphp
                            @foreach($dispatchDetails as $label => $value)
                                <tr>
                                    <td style="padding:10px; border:1px solid #eee; background:#f9fafb; font-weight:bold; width:35%;">{{ $label }}</td>
                                    <td style="padding:10px; border:1px solid #eee;">{{ $value }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <p>The attached PDF includes the full shipment route map, SOP inspection details, and
                            any FASTag toll / container tracking data collected during transit.</p>

                        <!-- Footer -->
                        <p style="margin-top:30px;"><strong>Thank you for choosing IL Cargo Care.</strong></p>
                        <p><strong>Best regards,</strong><br>IL Cargo Care Team</p>
                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background:#f9fafb; text-align:center; font-size:12px; color:#666; padding:15px;">
                        <p><strong>IL Cargo Care - Professional Cargo Inspection Services</strong></p>
                        <p>This is an automated final dispatch report generated by the IL Cargo Care system as
                            part of loss prevention measures; policy conditions shall prevail at all times.</p>
                        <p>For queries, please contact our support team.</p>
                        <p><strong>Report Generated:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y, h:i:s A') }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
