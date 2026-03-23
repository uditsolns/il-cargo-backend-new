<?php

namespace App\Http\Controllers;

use App\Services\ApiUsageReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ApiUsageReportController extends Controller
{
    private $reportService;

    public function __construct(ApiUsageReportService $reportService) {
        $this->reportService = $reportService;
    }

    /**
     * Overall summary — total calls, success rate, avg latency.
     *
     * GET /reports/api-usage/summary?from=2025-01-01&to=2025-01-31
     */
    public function summary(Request $request): JsonResponse
    {
        [$from, $to] = $this->resolveDateRange($request);

        return response()->json([
            'from'    => $from->toDateString(),
            'to'      => $to->toDateString(),
            'summary' => $this->reportService->summary($from, $to),
        ]);
    }

    /**
     * Defaults to the current month if no range is provided.
     * Caps the range at 90 days to prevent expensive unbounded queries.
     *
     * @return array{Carbon, Carbon}
     */
    private function resolveDateRange(Request $request): array
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        // Safety cap: never allow more than 90 days in one query
        if ($from->diffInDays($to) > 90) {
            $to = $from->copy()->addDays(90)->endOfDay();
        }

        return [$from, $to];
    }

    /**
     * Calls grouped by endpoint.
     *
     * GET /reports/api-usage/by-endpoint?from=2025-01-01&to=2025-01-31
     */
    public function byEndpoint(Request $request): JsonResponse
    {
        [$from, $to] = $this->resolveDateRange($request);

        return response()->json([
            'from'      => $from->toDateString(),
            'to'        => $to->toDateString(),
            'breakdown' => $this->reportService->byEndpoint($from, $to),
        ]);
    }

    /**
     * Calls grouped by user.
     *
     * GET /reports/api-usage/by-user?from=2025-01-01&to=2025-01-31
     */
    public function byUser(Request $request): JsonResponse
    {
        [$from, $to] = $this->resolveDateRange($request);

        return response()->json([
            'from'      => $from->toDateString(),
            'to'        => $to->toDateString(),
            'breakdown' => $this->reportService->byUser($from, $to),
        ]);
    }

    /**
     * Daily call volume trend.
     *
     * GET /reports/api-usage/daily-trend?from=2025-01-01&to=2025-01-31
     */
    public function dailyTrend(Request $request): JsonResponse
    {
        [$from, $to] = $this->resolveDateRange($request);

        return response()->json([
            'from'  => $from->toDateString(),
            'to'    => $to->toDateString(),
            'trend' => $this->reportService->dailyTrend($from, $to),
        ]);
    }
}
