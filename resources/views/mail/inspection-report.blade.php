{{-- resources/views/mail/inspection-report-mail.blade.php --}}
    <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cargo Inspection Report</title>
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
                        <h1 style="margin:0; font-size:20px; color:#ffffff;">Cargo Inspection Report</h1>
                    </td>
                </tr>

                <!-- BODY CONTENT -->
                <tr>
                    <td style="padding:30px;">
                        <p style="margin-top:0;">Dear <strong>{{ $cargo->group->name ?? 'Valued Customer' }}</strong>,
                        </p>

                        <p>We have completed the cargo inspection for your dispatch
                            <strong>{{ $cargo->dispatch_id }}</strong>. Please find the detailed report and summary
                            below.</p>

                        @if($summary['breachedCount'] > 0)
                            <div
                                style="border-left:5px solid #ffc107; background:#fff8e1; padding:15px; margin:20px 0; border-radius:4px;">
                                <h3 style="margin-top:0; color:#856404;">⚠️ Attention Required</h3>
                                <p><strong>{{ $summary['breachedCount'] }}</strong> out of
                                    <strong>{{ $summary['totalChecks'] }}</strong> inspection criteria have been
                                    breached as per the self-declaration entries of Insured's representative and require immediate attention.</p>
                                <p><strong>Compliance Rate:</strong> {{ $summary['complianceRate'] }}%</p>
                            </div>
                        @else
                            <div
                                style="border-left:5px solid #28a745; background:#e9f7ef; padding:15px; margin:20px 0; border-radius:4px;">
                                <h3 style="margin-top:0; color:#155724;">✅ All Checks Passed</h3>
                                <p>All <strong>{{ $summary['totalChecks'] }}</strong> Inspection Criteria have been complied  as per the self-declaration entries of Insured's representative.</p>
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
                                    'Invoice Value' => '₹'.number_format($cargo->invoice_value),
                                    'Inspection Date' => \Carbon\Carbon::parse($cargo->updated_at)->format('d/m/Y, h:i A'),
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

                        <!-- Inspection Summary -->
                        <h2 style="font-size:18px; border-bottom:2px solid #eee; padding-bottom:5px;">📊 Inspection
                            Summary</h2>
                        <table width="100%" cellspacing="0" cellpadding="0" border="0"
                               style="border-collapse:collapse; margin:15px 0; margin-bottom: 40px">
                            <tbody>
                            <tr>
                                <td style="padding:10px; border:1px solid #eee; background:#f9fafb; font-weight:bold;">
                                    Total SOPs Checked
                                </td>
                                <td style="padding:10px; border:1px solid #eee; text-align:center;">{{ $summary['totalChecks'] }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px; border:1px solid #eee; background:#f9fafb; font-weight:bold;">✅
                                    Compliant SOPs
                                </td>
                                <td style="padding:10px; border:1px solid #eee; color:#28a745; text-align:center; font-weight:bold;">{{ $summary['compliantCount'] }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px; border:1px solid #eee; background:#f9fafb; font-weight:bold;">
                                    ⚠️ Breached SOPs
                                </td>
                                <td style="padding:10px; border:1px solid #eee; color:#dc3545; text-align:center; font-weight:bold;">{{ $summary['breachedCount'] }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px; border:1px solid #eee; background:#f9fafb; font-weight:bold;">
                                    📸 Total Photographs
                                </td>
                                <td style="padding:10px; border:1px solid #eee; text-align:center;">{{ $summary['totalPhotographs'] }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px; border:1px solid #eee; background:#f9fafb; font-weight:bold;">
                                    🏗️ Zones Inspected
                                </td>
                                <td style="padding:10px; border:1px solid #eee; text-align:center;">{{ $summary['zonesInspected'] }}</td>
                            </tr>
                            <tr>
                                <td style="padding:10px; border:1px solid #007bff; background:#007bff; color:white; font-weight:bold;">
                                    Overall Compliance Rate
                                </td>
                                <td style="padding:10px; border:1px solid #007bff; background:#007bff; color:white; text-align:center; font-weight:bold; font-size:16px;">{{ $summary['complianceRate'] }}
                                    %
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <!-- Key Findings -->
                        <h2 style="font-size:18px; border-bottom:2px solid #eee; padding-bottom:5px;">🚨 Key
                            Findings</h2>
                        @if($summary['breachedCount'] > 0)
                            <p>The following inspection criteria require your immediate attention:</p>
                            @php
                                $breachedItems = collect($cargo->checklists)->where('is_sop_breached', 'yes');
                            @endphp
                            <ul style="margin-top:0;">
                                @foreach($breachedItems as $index => $item)
                                    <li>
                                        <strong>{{ $index + 1 }}.</strong> {{ $item['question'] }}<br>
                                        <small>Status: ❌ <strong>BREACHED</strong> | Required
                                            Compliance: {{ $item['preferred_compliance'] }}</small>
                                    </li>
                                @endforeach
                            </ul>
                            <div
                                style="border-left:5px solid #ffc107; background:#fff8e1; padding:15px; border-radius:4px; margin-top:15px; margin-bottom: 40px;">
                                <strong>Recommended Actions:</strong>
                                <ul>
                                    <li>Review the detailed PDF report attached</li>
                                    <li>Contact our inspection team for clarification</li>
                                    <li>Implement corrective measures before transportation</li>
                                    <li>Consider re-inspection if critical safety items are affected</li>
                                </ul>
                            </div>
                        @else
                            <p>Excellent news! Your cargo has passed all inspection criteria successfully. The cargo is
                                ready for safe transportation.</p>
                            <div
                                style="border-left:5px solid #28a745; background:#e9f7ef; padding:15px; border-radius:4px; margin-bottom: 40px;">
                                <strong>All Systems Green:</strong>
                                <ul>
                                    <li>✅ All safety protocols met</li>
                                    <li>✅ Cargo properly secured</li>
                                    <li>✅ Documentation verified</li>
                                    <li>✅ Ready for transportation</li>
                                </ul>
                            </div>
                        @endif

                        <!-- Footer -->
                        <p style="margin-top:30px;"><strong>Thank you for choosing IL Cargo Care for your inspection
                                needs.</strong></p>
                        <p>We are committed to ensuring the safe transportation of your valuable cargo.</p>
                        <p><strong>Best regards,</strong><br>IL Cargo Care Inspection Team</p>
                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background:#f9fafb; text-align:center; font-size:12px; color:#666; padding:15px;">
                        <p><strong>IL Cargo Care - Professional Cargo Inspection Services</strong></p>
                        <p>Please be inform that this is the sop for this particular consignment, the self inspection results highlight the concern that the dispatch is not safe and does not mee the criteria of the SOP.</p>
                        <p>In an event of any incident that may course damage to the consignment, policy terms and conditions prevail which may lead to denial of the claim. You are requested to kindly explain the breach of the SOP and take corrective actionable before dispatching the cargo.</p>
                        <p>Any concern kindly communicate with Mr. Sovan Bose on () or Mr. Kedar Parab () or you ma also write to mws@icicilombard.com.</p>
                        <p>This is an automated cargo inspection report generated by the IL Cargo Care system which is assigned by ICICI Lombard GIC's Risk Management Team as part of loss prevention measures, policy conditions shall prevell at all times.</p>
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
