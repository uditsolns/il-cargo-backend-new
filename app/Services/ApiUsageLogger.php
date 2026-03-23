<?php

namespace App\Services;

use App\Models\ApiUsageLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ApiUsageLogger
{

    public function log(
        string $service,
        string $endpoint,
        array  $requestPayload,
        int    $httpStatus,
        bool   $success,
        array  $responseSummary,
        int    $latencyMs,
        Carbon $requestedAt,
    ): void {

        ApiUsageLog::create([
            'user_id'          => Auth::user()->id,
            'service'          => $service,
            'endpoint'         => $endpoint,
            'request_payload'  => $requestPayload,
            'http_status'      => $httpStatus,
            'success'          => $success,
            'response_summary' => $responseSummary,
            'latency_ms'       => $latencyMs,
            'ip_address'       => Request::ip(),
            'requested_at'     => $requestedAt,
        ]);
    }
}
