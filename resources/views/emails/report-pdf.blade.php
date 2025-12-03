<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargo Inspection Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }

        .header p {
            color: #666;
            margin: 5px 0 0 0;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h2 {
            color: #007bff;
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .section h3 {
            color: #495057;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 12px;
            margin-top: 25px;
        }

        .section h4 {
            color: #6c757d;
            margin-bottom: 10px;
            margin-top: 20px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-compliant {
            background-color: #d4edda;
            color: #155724;
        }

        .status-breached {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }

        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }

        .zone-container {
            margin-bottom: 40px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .zone-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .zone-title {
            color: #495057;
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .zone-description {
            color: #6c757d;
            margin: 5px 0 0 0;
            font-size: 14px;
        }

        .phase-container {
            padding: 20px;
            border-bottom: 1px solid #f1f3f4;
        }

        .phase-container:last-child {
            border-bottom: none;
        }

        .phase-title {
            color: #6c757d;
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .phase-icon {
            width: 20px;
            height: 20px;
            background-color: #007bff;
            border-radius: 50%;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }

        .photo-item {
            background-color: #ffffff;
            padding: 0;
            border-radius: 12px;
            border: 2px solid #e9ecef;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        .photo-item img {
            width: 100%;
            height: auto;
            max-height: 250px;
            object-fit: contain;
            background-color: #f8f9fa;
            display: block;
        }

        .photo-details {
            text-align: left;
            padding: 15px;
        }

        .photo-name {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 8px;
            font-size: 15px;
            text-align: center;
        }

        .photo-meta {
            color: #6c757d;
            font-size: 13px;
            line-height: 1.5;
        }

        .photo-meta-item {
            display: flex;
            align-items: center;
            margin-bottom: 4px;
        }

        .photo-meta-item:last-child {
            margin-bottom: 0;
        }

        .meta-icon {
            width: 16px;
            margin-right: 8px;
            text-align: center;
        }

        .no-photos {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 6px;
            margin-top: 15px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            table {
                font-size: 14px;
            }

            table th,
            table td {
                padding: 8px 6px;
            }

            .photo-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .phase-container {
                padding: 15px;
            }

            .zone-header {
                padding: 12px 15px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Cargo Inspection Report</h1>
        <p>Dispatch ID: <strong>{{ $cargo['dispatch_id'] }}</strong></p>
    </div>

    <!-- Alert for SOP Breaches -->
    @php
        $breachedCount = collect($cargo['checklists'])->where('is_sop_breached', 'yes')->count();
        $totalChecks = count($cargo['checklists']);
    @endphp

    @if($breachedCount > 0)
        <div class="alert alert-warning">
            <strong>⚠️ SOP Compliance Alert:</strong>
            {{ $breachedCount }} out of {{ $totalChecks }} inspection criteria have been breached.
            Please review the detailed report below.
        </div>
    @else
        <div class="alert alert-info">
            <strong>✅ SOP Compliance:</strong>
            All {{ $totalChecks }} inspection criteria have been met successfully.
        </div>
    @endif

    <!-- Dispatch Details Section -->
    <div class="section">
        <h2>📦 Dispatch Details</h2>
        <table>
            <tbody>
            <tr>
                <th style="width: 25%;">Dispatch ID</th>
                <td colspan="3">{{ $cargo['dispatch_id'] }}</td>
            </tr>
            <tr>
                <th>Dispatch Date</th>
                <td>{{ \Carbon\Carbon::parse($cargo['created_at'])->format('d-m-Y') }}</td>
                <th>Dispatch Type</th>
                <td>{{ $cargo['dispatch_type'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Survey Location</th>
                <td colspan="3">
                    @if(isset($cargo['group']['address']) && isset($cargo['group']['city']))
                        {{ $cargo['group']['address'] }}, {{ $cargo['group']['city'] }}
                    @else
                        Location not specified
                    @endif
                </td>
            </tr>
            <tr>
                <th>Survey Date/Time</th>
                <td colspan="3">{{ \Carbon\Carbon::parse($cargo['updated_at'])->format('d/m/Y, h:i:s A') }}</td>
            </tr>
            <tr>
                <th>Vehicle Registration</th>
                <td>{{ $cargo['veh_reg_no'] }}</td>
                <th>Cargo Unit Serial</th>
                <td>{{ $cargo['cargo_unit_serial_no'] }}</td>
            </tr>
            <tr>
                <th>Vehicle Capacity (kg)</th>
                <td>{{ number_format($cargo['veh_carrying_capacity']) }}</td>
                <th>Serial Number</th>
                <td>{{ $cargo['serial_no'] }}</td>
            </tr>
            <tr>
                <th>Invoice Value (₹)</th>
                <td>{{ number_format($cargo['invoice_value']) }}</td>
                <th>Value Add (₹)</th>
                <td>{{ number_format($cargo['value_add']) }}</td>
            </tr>
            <tr>
                <th>Destination Address</th>
                <td colspan="3">{{ $cargo['address'] ?? 'Not specified' }}</td>
            </tr>
            <tr>
                <th>Destination PIN</th>
                <td>{{ $cargo['destination_pin'] ?? 'N/A' }}</td>
                <th>Origin PIN</th>
                <td>{{ $cargo['origin_pin'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Transit Date</th>
                <td>{{ $cargo['date_transit'] ? \Carbon\Carbon::parse($cargo['date_transit'])->format('d-m-Y') : 'N/A' }}</td>
                <th>Flat Track Number</th>
                <td>{{ $cargo['flat_track_number'] === 'null' ? 'N/A' : $cargo['flat_track_number'] }}</td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Group/Customer Details Section -->
    @if(isset($cargo['group']))
        <div class="section">
            <h2>🏢 Customer Details</h2>
            <table>
                <tbody>
                <tr>
                    <th style="width: 25%;">Company Name</th>
                    <td colspan="3">{{ $cargo['group']['name'] }}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td colspan="3">{{ $cargo['group']['address'] }}</td>
                </tr>
                <tr>
                    <th>City</th>
                    <td>{{ $cargo['group']['city'] }}</td>
                    <th>GST</th>
                    <td>{{ $cargo['group']['gst'] === 'NA' ? 'Not Available' : $cargo['group']['gst'] }}</td>
                </tr>
                @if(isset($cargo['group']['additional_emails']) && is_array($cargo['group']['additional_emails']))
                    <tr>
                        <th>Additional Emails</th>
                        <td colspan="3">{{ implode(', ', $cargo['group']['additional_emails']) }}</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    @endif

    <!-- SOP/STA Details Section -->
    <div class="section">
        <h2>📋 SOP/STA Inspection Details</h2>
        <table>
            <thead>
            <tr>
                <th style="width: 8%;">Sr.No</th>
                <th style="width: 50%;">Inspection Criteria</th>
                <th style="width: 15%;">Record</th>
                <th style="width: 15%;">Compliance</th>
                <th style="width: 12%;">Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($cargo['checklists'] as $index => $checklist)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $checklist['question'] }}</td>
                    <td>{{ $checklist['answer'] == '1' ? 'Yes' : 'No' }}</td>
                    <td>{{ $checklist['preferred_compliance'] }}</td>
                    <td>
                        @if($checklist['is_sop_breached'] === 'yes')
                            <span class="status-badge status-breached">BREACHED</span>
                        @else
                            <span class="status-badge status-compliant">COMPLIANT</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Photographs Section (Grouped by Zone/Phase) -->
    @if(isset($cargo['photographs']) && count($cargo['photographs']) > 0)
        <div class="section">
            <h2>📷 Inspection Photographs</h2>

            @php
                // Group photos by actual zone (which is stored in phase object due to DB structure)
                $photosByZone = collect($cargo['photographs'])->groupBy(function($photo) {
                    return $photo['phase']['name'] ?? 'Unknown Zone';
                });
            @endphp

            @foreach($photosByZone as $zoneName => $zonePhotos)
                <div class="zone-container">
                    <div class="zone-header">
                        <h3 class="zone-title">🏗️ {{ $zoneName }}</h3>
                        <p class="zone-description">
                            {{ $zonePhotos->first()['phase']['description'] ?? 'Zone inspection area' }}
                        </p>
                    </div>

                    @php
                        // Group photos within this zone by actual phase (which is stored in zone object)
                        $photosByPhase = $zonePhotos->groupBy(function($photo) {
                            return $photo['zone']['name'] ?? 'Unknown Phase';
                        });
                    @endphp

                    @foreach($photosByPhase as $phaseName => $phasePhotos)
                        <div class="phase-container">
                            <h4 class="phase-title">
                                <span class="phase-icon">{{ $loop->iteration }}</span>
                                {{ $phaseName }}
                            </h4>
                            <p style="color: #6c757d; font-size: 13px; margin-bottom: 15px;">
                                {{ $phasePhotos->first()['zone']['description'] ?? 'Phase inspection details' }}
                            </p>

                            @if($phasePhotos->count() > 0)
                                <div class="photo-grid">
                                    @foreach($phasePhotos as $photo)
                                        <div class="photo-item">
                                            <img
                                                src="{{ "http://ilcargocare.com/ilcargo-backend-new/storage/app/public/photos/".$photo['photo'] }}"
                                                alt="{{ $photo['name'] }}" loading="lazy">
                                            <div class="photo-details">
                                                <div class="photo-name">{{ $photo['name'] }}</div>
                                                <div class="photo-meta">
                                                    <div class="photo-meta-item">
                                                        <span class="meta-icon">🕒</span>
                                                        <span>{{ $photo['time'] }}</span>
                                                    </div>
                                                    @if($photo['latitude'] && $photo['longitude'])
                                                        <div class="photo-meta-item">
                                                            <span class="meta-icon">📍</span>
                                                            <span>{{ number_format($photo['latitude'], 6) }}, {{ number_format($photo['longitude'], 6) }}</span>
                                                        </div>
                                                    @else
                                                        <div class="photo-meta-item">
                                                            <span class="meta-icon">📍</span>
                                                            <span>Coordinates not available</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="no-photos">
                                    No photographs available for this phase
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif

    <!-- Summary Section -->
    <div class="section">
        <h2>📊 Inspection Summary</h2>
        <table>
            <tbody>
            <tr>
                <th style="width: 40%;">Total SOPs</th>
                <td>{{ $totalChecks }}</td>
            </tr>
            <tr>
                <th>Compliant SOPs</th>
                <td style="color: #155724;">{{ $totalChecks - $breachedCount }}</td>
            </tr>
            <tr>
                <th>Breached SOPs</th>
                <td style="color: #721c24;">{{ $breachedCount }}</td>
            </tr>
            <tr>
                <th>Overall Compliance Rate</th>
                <td>
                    @php $complianceRate = $totalChecks > 0 ? round((($totalChecks - $breachedCount) / $totalChecks) * 100, 1) : 0; @endphp
                    <strong>{{ $complianceRate }}%</strong>
                </td>
            </tr>
            <tr>
                <th>Inspection Status</th>
                <td>
                    @if($breachedCount === 0)
                        <span class="status-badge status-compliant">PASSED</span>
                    @else
                        <span class="status-badge status-breached">ATTENTION REQUIRED</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Total Photographs</th>
                <td>{{ count($cargo['photographs']) ?? 0 }}</td>
            </tr>
            @if(isset($cargo['photographs']) && count($cargo['photographs']) > 0)
                <tr>
                    <th>Inspection Zones Covered</th>
                    <td>{{ collect($cargo['photographs'])->groupBy('phase.name')->count() }}</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This is an automated cargo inspection report generated by the IL Cargo Care system.</p>
        <p>For any queries, please contact our support team.</p>
        <p><strong>Report Generated:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y, h:i:s A') }}</p>
    </div>
</div>
</body>
</html>
