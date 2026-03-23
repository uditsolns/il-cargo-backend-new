<?php

namespace App\Services;

use App\Models\ApiUsageLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ApiUsageReportService
{
    /**
     * Overall summary for a given date range.
     * Used by the dashboard card and the summary report endpoint.
     */
    public function summary(Carbon $from, Carbon $to): array
    {
        $stats = ApiUsageLog::whereBetween('requested_at', [$from, $to])
            ->selectRaw('
                COUNT(*)                                    AS total_calls,
                SUM(CASE WHEN success THEN 1 ELSE 0 END)   AS successful_calls,
                SUM(CASE WHEN success THEN 0 ELSE 1 END)   AS failed_calls,
                ROUND(AVG(latency_ms))                     AS avg_latency_ms
            ')
            ->first();

        $total = (int) $stats->total_calls;

        return [
            'total_calls'      => $total,
            'successful_calls' => (int) $stats->successful_calls,
            'failed_calls'     => (int) $stats->failed_calls,
            'success_rate'     => $total > 0
                ? round(($stats->successful_calls / $total) * 100, 2)
                : 0,
            'avg_latency_ms'   => (int) $stats->avg_latency_ms,
        ];
    }

    /**
     * Breakdown of calls grouped by endpoint.
     */
    public function byEndpoint(Carbon $from, Carbon $to): Collection
    {
        return ApiUsageLog::whereBetween('requested_at', [$from, $to])
            ->selectRaw('
                endpoint,
                COUNT(*)                                    AS total_calls,
                SUM(CASE WHEN success THEN 1 ELSE 0 END)   AS successful_calls,
                SUM(CASE WHEN success THEN 0 ELSE 1 END)   AS failed_calls,
                ROUND(AVG(latency_ms))                     AS avg_latency_ms
            ')
            ->groupBy('endpoint')
            ->orderByDesc('total_calls')
            ->get();
    }

    /**
     * Breakdown of calls grouped by user.
     */
    public function byUser(Carbon $from, Carbon $to): Collection
    {
        return ApiUsageLog::whereBetween('requested_at', [$from, $to])
            ->selectRaw('
                user_id,
                COUNT(*)                                    AS total_calls,
                SUM(CASE WHEN success THEN 1 ELSE 0 END)   AS successful_calls,
                SUM(CASE WHEN success THEN 0 ELSE 1 END)   AS failed_calls
            ')
            ->with('user:id,name,email')
            ->groupBy('user_id')
            ->orderByDesc('total_calls')
            ->get();
    }

    /**
     * Daily volume trend — useful for charts on the frontend.
     */
    public function dailyTrend(Carbon $from, Carbon $to): Collection
    {
        return ApiUsageLog::whereBetween('requested_at', [$from, $to])
            ->selectRaw("
                DATE(requested_at)                          AS date,
                COUNT(*)                                    AS total_calls,
                SUM(CASE WHEN success THEN 1 ELSE 0 END)   AS successful_calls,
                SUM(CASE WHEN success THEN 0 ELSE 1 END)   AS failed_calls
            ")
            ->groupByRaw('DATE(requested_at)')
            ->orderBy('date')
            ->get();
    }
}
