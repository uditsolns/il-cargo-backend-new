{{-- resources/views/pdf/inspection-report.blade.php --}}
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargo Inspection Report - {{ $cargo['dispatch_id'] }}</title>
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
            /*padding-bottom: 20px;*/
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
            /*margin: 0 auto 15px auto;*/
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

        /* DOMPDF-compatible photo grid using table layout */
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
            /*text-align: center;*/
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
            page-break-inside: avoid;
        }

        /* Empty cell styling for incomplete rows */
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

        /* Location Map Section */
        .map-container {
            position: relative;
            /*width: 100%;*/
            height: 200px;
            border: 2px solid #007bff;
            border-radius: 8px;
            background-color: #f8f9fa;
            margin-bottom: 20px;
            overflow: hidden;
            padding: 30px 20px;
        }

        .map-canvas {
            position: relative;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, #e3f2fd 1px, transparent 1px);
            background-size: 50px 100%;
        }

        .map-marker {
            position: absolute;
            width: 40px;
            height: 50px;
            transform: translateX(-50%);
            text-align: center;
            top: 40%;
        }

        .marker-pin {
            position: relative;
            width: 30px;
            height: 30px;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            margin: 0 auto 12px;
            border: 3px solid #fff;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .marker-number {
            font-size: 12px;
            font-weight: bold;
            color: #fff;
            line-height: 1;
            position: absolute;
            top: 0;
            left: 0;
            transform: rotate(45deg) translate(220%, -20%);
        }

        .marker-label {
            font-size: 10px;
            font-weight: bold;
            color: #333;
            text-align: center;
            width: 80px;
            margin-left: -20px;
            line-height: 1.1;
            word-wrap: break-word;
            margin-top: 3px;
        }

        .photo-count {
            font-size: 8px;
            color: #666;
            margin-top: 2px;
            line-height: 1;
        }

        .marker-start .marker-pin {
            background-color: #28a745;
        }

        .marker-intermediate .marker-pin {
            background-color: #ffc107;
        }

        .marker-intermediate .marker-number {
            color: #212529;
        }

        .marker-end .marker-pin {
            background-color: #dc3545;
        }

        .map-arrow {
            position: absolute;
            top: 50%;
            height: 2px;
            background-color: #007bff;
            transform: translateY(-50%);
            z-index: 5;
        }

        .map-arrow::after {
            content: '';
            position: absolute;
            right: -6px;
            top: -4px;
            width: 0;
            height: 0;
            border-left: 8px solid #007bff;
            border-top: 5px solid transparent;
            border-bottom: 5px solid transparent;
        }

        .location-legend {
            display: table;
            width: 100%;
            margin-top: 15px;
            font-size: 11px;
        }

        .legend-item {
            display: table-cell;
            text-align: center;
            padding: 8px;
            vertical-align: middle;
        }

        .legend-marker {
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            border: 2px solid #fff;
            margin-right: 8px;
            vertical-align: middle;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .legend-start {
            background-color: #28a745;
        }

        .legend-intermediate {
            background-color: #ffc107;
        }

        .legend-end {
            background-color: #dc3545;
        }

        .location-details {
            margin-top: 15px;
        }

        .location-details table th {
            background-color: #e9ecef;
            width: 20%;
        }

        .location-details table th {
            background-color: #e9ecef;
        }

        .location-details table th:nth-child(1) {
            width: 15%;
        }

        .location-details table th:nth-child(2) {
            width: 25%;
        }

        .location-details table th:nth-child(3) {
            width: 10%;
        }

        .location-details table th:nth-child(4) {
            width: 20%;
        }

        .location-details table th:nth-child(5) {
            width: 30%;
        }

        /* Replace the existing map-related CSS with these styles */
        .google-map-container {
            position: relative;
            /*width: 100%;*/
            margin-bottom: 40px;
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

        /* Keep the existing legend styles but update colors to match Google Maps markers */
        .legend-start {
            background-color: #34a853; /* Google green */
        }

        .legend-intermediate {
            background-color: #fbbc04; /* Google yellow */
        }

        .legend-end {
            background-color: #ea4335; /* Google red */
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
            <h1>Cargo Inspection Report</h1>
            <p>Dispatch ID: <strong>{{ $cargo['dispatch_id'] }}</strong></p>
        </td>
    </tr>


</table>

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

{{-- Location Tracking Map --}}
@if(isset($locationData) && count($locationData) > 0)
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
                @foreach($locationData as $index => $location)
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
                                $rows = $photos->chunk(3); // 3 photos per row
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

                                        {{-- Fill remaining cells if row has less than 3 photos --}}
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
