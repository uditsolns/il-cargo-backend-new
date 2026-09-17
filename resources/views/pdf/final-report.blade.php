{{-- resources/views/pdf/final-report.blade.php --}}
{{-- Cloned from pdf/inspection-report.blade.php - adds the shipment route
     map, FASTag transactions, and container tracking sections for the
     trip-completion "final report". The original is left untouched. --}}
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Dispatch Report - {{ $cargo['dispatch_id'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #fff;
            max-width: 800px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            margin-bottom: 30px;
        }

        table.header td, tr {
            padding: 0;
            border: none;
        }

        table.header td:first-child {
            padding-left: 30px;
            margin-right: 0;
        }

        .header img {
            display: block;
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
            page-break-inside: avoid;
        }

        .section h2 {
            color: #007bff;
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-size: 18px;
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
            padding: 8px;
            text-align: left;
            font-size: 14px;
            vertical-align: top;
        }

        table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }

        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
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
            font-size: 14px;
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

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .photo-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        .photo-grid td {
            width: 33.33%;
            padding: 8px;
            border: none;
            vertical-align: top;
        }

        .photo-item {
            background-color: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
            width: 100%;
            page-break-inside: avoid;
        }

        .photo-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            background-color: #f8f9fa;
            display: block;
        }

        .photo-details {
            padding: 8px;
            font-size: 10px;
        }

        .photo-name {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 3px;
            font-size: 10px;
        }

        .photo-meta {
            color: #6c757d;
            font-size: 9px;
            line-height: 1.2;
        }

        .zone-section {
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .zone-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .zone-title {
            color: #495057;
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .zone-content {
            padding: 15px;
        }

        .phase-title {
            color: #6c757d;
            margin: 15px 0 10px 0;
            font-size: 14px;
            font-weight: 600;
        }

        .summary td {
            text-align: center;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
            page-break-inside: avoid;
        }

        .empty-cell {
            border: none !important;
            padding: 8px;
        }

        @media print {
            body {
                margin: 0;
                padding: 15px;
            }

            .section {
                page-break-inside: avoid;
            }
        }

        .google-map-container {
            position: relative;
            margin-bottom: 20px;
            text-align: center;
            border: 2px solid #007bff;
            border-radius: 8px;
            overflow: hidden;
            background-color: #f8f9fa;
            padding: 10px;
        }

        .google-map-image {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .route-endpoints {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .route-endpoints th {
            background-color: #e9ecef;
        }

        .legend-start {
            background-color: #34a853;
        }

        .legend-intermediate {
            background-color: #fbbc04;
        }

        .legend-end {
            background-color: #ea4335;
        }

        .kv-table td:first-child {
            width: 30%;
            font-weight: bold;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
<table class="header">
    <tr>
        <td>
            <img src="http://ilcargocare.com/ilcargo-backend-new/storage/app/public/glob-3600-logo.jpg" alt="Globe 360 Cloud Logistic"
                 style="max-width:100px; height:auto; margin-bottom:15px;">
        </td>
        <td>
            <h1>Final Dispatch Report</h1>
            <p>Dispatch ID: <strong>{{ $cargo['dispatch_id'] }}</strong></p>
        </td>
    </tr>
</table>

<div class="alert alert-success">
    <strong>Trip Completed:</strong> This is the final report for this dispatch, generated after survey completion.
</div>

{{-- SOP Breach Alert --}}
@if($summary['breachedCount'] > 0)
    <div class="alert alert-warning">
        <strong>SOP Compliance Alert:</strong>
        {{ $summary['breachedCount'] }} out of {{ $summary['totalChecks'] }} inspection criteria have been breached as per the self-declaration entries of Insured's representative.
    </div>
@else
    <div class="alert alert-info">
        <strong>SOP Compliance:</strong>
        All {{ $summary['totalChecks'] }} Inspection Criteria have been complied  as per the self-declaration entries of Insured's representative
    </div>
@endif

{{-- Dispatch Details --}}
<div class="section">
    <h2>Dispatch Details</h2>
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
            <td>{{ number_format(floatval($cargo['invoice_value'])) ?? 'N/A'}}</td>
            <th>Value Add (₹)</th>
            <td>{{ number_format(floatval($cargo['value_add'])) ?? 'N/A'}}</td>
        </tr>
        <tr>
            <th>Estimated Date of Arrival</th>
            <td colspan="3">{{ $cargo['estimated_date_of_arrival'] ?? 'N/A' }}</td>
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
        </tbody>
    </table>
</div>

{{-- Customer Details --}}
@if(isset($cargo['group']))
    <div class="section">
        <h2>Customer Details</h2>
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
            </tbody>
        </table>
    </div>
@endif

{{-- Shipment Route Map (NEW) --}}
<div class="section">
    <h2>Shipment Route</h2>

    @if(isset($routeMapUrl) && $routeMapUrl)
        <div class="google-map-container">
            <img src="{{ $routeMapUrl }}" alt="Shipment route from origin to destination" class="google-map-image">
        </div>
        <table class="route-endpoints">
            <thead>
            <tr>
                <th style="width: 10%;"></th>
                <th>Location</th>
                <th style="width: 30%;">Coordinates</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><span class="legend-marker legend-start" style="display:inline-block;width:12px;height:12px;border-radius:50%;"></span> Origin</td>
                <td>{{ $cargo['address'] ?? 'N/A' }}</td>
                <td>{{ $originLat ?? 'N/A' }}, {{ $originLng ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><span class="legend-marker legend-end" style="display:inline-block;width:12px;height:12px;border-radius:50%;"></span> Destination</td>
                <td>{{ $cargo['destination_address'] ?? 'N/A' }}</td>
                <td>{{ $destinationLat ?? 'N/A' }}, {{ $destinationLng ?? 'N/A' }}</td>
            </tr>
            </tbody>
        </table>
    @else
        <div class="alert alert-info">
            <strong>Route map unavailable:</strong> Origin and/or destination coordinates were not recorded for this dispatch.
        </div>
    @endif
</div>

{{-- SOP Details --}}
<div class="section">
    <h2>SOP Inspection Details</h2>
    <table>
        <thead>
        <tr>
            <th style="width: 5%;">Sr.</th>
            <th style="width: 55%;">Inspection Criteria</th>
            <th style="width: 10%;">Record</th>
            <th style="width: 15%;">Compliance</th>
            <th style="width: 15%;">Status</th>
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

{{-- Inspection Locations (photo GPS trail) --}}
@if(isset($mapLocations) && count($mapLocations) > 0)
    <div class="section">
        <h2>Inspection Locations</h2>

        @if(isset($mapImageUrl) && $mapImageUrl)
            <div class="google-map-container">
                <img src="{{ $mapImageUrl }}" alt="Inspection Locations Map" class="google-map-image">
            </div>
        @else
            <div class="alert alert-info">
                <strong>Map Unavailable:</strong> Location map could not be generated at this time.
            </div>
        @endif

        <div class="location-details">
            <table>
                <thead>
                <tr>
                    <th>Sequence</th>
                    <th>Zone Name</th>
                    <th>Photos</th>
                    <th>Inspection Time</th>
                    <th>Coordinates</th>
                </tr>
                </thead>
                <tbody>
                @foreach($mapLocations as $index => $location)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $location['zone_name'] }}</td>
                        <td>{{ $location['photo_count'] }}</td>
                        <td>{{ $location['time'] }}</td>
                        <td>{{ number_format($location['latitude'], 6) }}
                            , {{ number_format($location['longitude'], 6) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- FASTag Transactions (NEW) --}}
<div class="section">
    <h2>FASTag Transactions</h2>
    @if(isset($fastagTransactions) && count($fastagTransactions) > 0)
        @foreach($fastagTransactions as $index => $transaction)
            <table class="kv-table">
                <tbody>
                <tr>
                    <td colspan="2" style="background-color:#e9ecef;">Transaction {{ $index + 1 }} - fetched {{ \Carbon\Carbon::parse($transaction['fetched_at'])->format('d-m-Y H:i') }}</td>
                </tr>
                @foreach($transaction['payload'] as $key => $value)
                    @if(!is_array($value))
                        <tr>
                            <td>{{ \Illuminate\Support\Str::headline($key) }}</td>
                            <td>{{ $value }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        @endforeach
    @else
        <div class="alert alert-info">
            <strong>No FASTag data available</strong> for this dispatch's vehicle at the time this report was generated.
        </div>
    @endif
</div>

{{-- Container Tracking (NEW) - only shown when this dispatch actually has a
     valid-format container number and tracking data was fetched for it; not
     every dispatch is a container shipment (could be a truck or other unit),
     so there's no "unavailable" fallback here, unlike FASTag above. --}}
@if(isset($containerTrackingEvents) && count($containerTrackingEvents) > 0)
    <div class="section">
        <h2>Container Tracking</h2>
        @foreach($containerTrackingEvents as $index => $event)
            <table class="kv-table">
                <tbody>
                <tr>
                    <td colspan="2" style="background-color:#e9ecef;">Event {{ $index + 1 }} - fetched {{ \Carbon\Carbon::parse($event['fetched_at'])->format('d-m-Y H:i') }}</td>
                </tr>
                @foreach($event['payload'] as $key => $value)
                    @if(!is_array($value))
                        <tr>
                            <td>{{ \Illuminate\Support\Str::headline($key) }}</td>
                            <td>{{ $value }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
@endif

{{-- Photographs Section --}}
@if(isset($cargo['photographs']) && count($cargo['photographs']) > 0)
    <div class="section">
        <h2>Inspection Photographs</h2>

        @php
            $photosByZone = collect($cargo['photographs'])->groupBy(function($photo) {
                return $photo['phase']['name'] ?? 'Unknown Zone';
            });
        @endphp

        @foreach($photosByZone as $zoneName => $zonePhotos)
            <div class="zone-section">
                <div class="zone-header">
                    <h3 class="zone-title">{{ $zoneName }}</h3>
                </div>
                <div class="zone-content">
                    @php
                        $photosByPhase = $zonePhotos->groupBy(function($photo) {
                            return $photo['zone']['name'] ?? 'Unknown Phase';
                        });
                    @endphp

                    @foreach($photosByPhase as $phaseName => $phasePhotos)
                        <h4 class="phase-title">{{ $phaseName }}</h4>

                        @if($phasePhotos->count() > 0)
                            @php
                                $photos = $phasePhotos->values();
                                $rows = $photos->chunk(3);
                            @endphp

                            <table class="photo-grid">
                                @foreach($rows as $row)
                                    <tr>
                                        @foreach($row as $photo)
                                            <td>
                                                <div class="photo-item">
                                                    <img
                                                        src="{{ "http://ilcargocare.com/ilcargo-backend-new/storage/app/public/photos/".$photo['photo'] }}"
                                                        alt="{{ $photo['name'] }}">
                                                    <div class="photo-details">
                                                        <div class="photo-name">{{ $photo['name'] }}</div>
                                                        <div class="photo-meta">
                                                            Time: {{ $photo['time'] }}
                                                            @if($photo['latitude'] && $photo['longitude'])
                                                                <br>GPS: {{ number_format($photo['latitude'], 4) }}
                                                                , {{ number_format($photo['longitude'], 4) }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        @endforeach

                                        @for($i = $row->count(); $i < 3; $i++)
                                            <td class="empty-cell"></td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </table>
                        @endif
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Summary Section --}}
<div class="section summary">
    <h2>Inspection Summary</h2>
    <table>
        <tbody>
        <tr>
            <th style="width: 40%;">Total SOPs</th>
            <td>{{ $summary['totalChecks'] }}</td>
        </tr>
        <tr>
            <th>Compliant SOPs</th>
            <td style="color: #155724;">{{ $summary['compliantCount'] }}</td>
        </tr>
        <tr>
            <th>Breached SOPs</th>
            <td style="color: #721c24;">{{ $summary['breachedCount'] }}</td>
        </tr>
        <tr>
            <th>Overall Compliance Rate</th>
            <td><strong>{{ $summary['complianceRate'] }}%</strong></td>
        </tr>
        <tr>
            <th>Inspection Status</th>
            <td>
                @if($summary['breachedCount'] === 0)
                    <span class="status-badge status-compliant">PASSED</span>
                @else
                    <span class="status-badge status-breached">ATTENTION REQUIRED</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Total Photographs</th>
            <td>{{ $summary['totalPhotographs'] }}</td>
        </tr>
        <tr>
            <th>Zones Inspected</th>
            <td>{{ $summary['zonesInspected'] }}</td>
        </tr>
        </tbody>
    </table>
</div>

<div class="footer">
    <p><strong>IL Cargo Care - Professional Cargo Inspection Services</strong></p>
    <p>Please be inform that this is the sop for this particular consignment, the self inspection results highlight the concern that the dispatch is not safe and does not mee the criteria of the SOP.</p>
    <p>In an event of any incident that may course damage to the consignment, policy terms and conditions prevail which may lead to denial of the claim. You are requested to kindly explain the breach of the SOP and take corrective actionable before dispatching the cargo.</p>
    <p>Any concern kindly communicate with Mr. Sovan Bose on () or Mr. Kedar Parab () or you ma also write to <i>mws@icicilombard.com</i>.</p>
    <p>This is an automated cargo inspection report generated by the IL Cargo Care system which is assigned by ICICI Lombard GIC's Risk Management Team as part of loss prevention measures, policy conditions shall prevell at all times.</p>
    <p>For queries, please contact our support team.</p>
    <p><strong>Report Generated:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y, h:i:s A') }}</p>
</div>
</body>
</html>
