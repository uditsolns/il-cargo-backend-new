<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Resolves the dashboard's date-range filter. `month`/`quarter`/`year`
 * pick an ACTUAL calendar period the caller names (e.g. month=8&year=2026
 * for August 2026), not "the current one" - a user browsing dashboard
 * history needs to pick any past period, not just today's.
 */
class DashboardPeriod
{
    public const DEFAULT = 'last_7_days';

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    public static function resolve(Request $request): array
    {
        $period = self::resolvePeriodLabel($request);

        [$from, $to] = match ($period) {
            'month' => self::month((int) $request->input('year'), (int) $request->input('month')),
            'quarter' => self::quarter((int) $request->input('year'), (int) $request->input('quarter')),
            'year' => self::year((int) $request->input('year')),
            'custom' => self::custom($request),
            default => [Carbon::now()->subDays(7)->startOfDay(), Carbon::now()->endOfDay()],
        };

        return [$from, $to, $period];
    }

    /**
     * How coarsely a time-series graph should bucket this period - a daily
     * point per day is unreadable over a quarter/year (~90/365 points), so
     * longer periods bucket by week/month instead. A custom range picks by
     * its own span, since it isn't one of the named periods.
     */
    public static function granularity(string $period, Carbon $from, Carbon $to): string
    {
        return match ($period) {
            'quarter' => 'week',
            'year' => 'month',
            'custom' => self::granularityForSpan($from, $to),
            default => 'day',
        };
    }

    private static function granularityForSpan(Carbon $from, Carbon $to): string
    {
        $days = $from->diffInDays($to);

        return match (true) {
            $days <= 31 => 'day',
            $days <= 180 => 'week',
            default => 'month',
        };
    }

    private static function resolvePeriodLabel(Request $request): string
    {
        $period = $request->input('period');

        if (!$period && ($request->filled('from_date') || $request->filled('to_date'))) {
            $period = 'custom';
        }

        return $period ?? self::DEFAULT;
    }

    private static function month(int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();

        return [$start, $start->copy()->endOfMonth()];
    }

    private static function quarter(int $year, int $quarter): array
    {
        $start = Carbon::create($year, (($quarter - 1) * 3) + 1, 1)->startOfMonth();

        return [$start, $start->copy()->addMonths(2)->endOfMonth()];
    }

    private static function year(int $year): array
    {
        $start = Carbon::create($year, 1, 1)->startOfYear();

        return [$start, $start->copy()->endOfYear()];
    }

    private static function custom(Request $request): array
    {
        $from = $request->filled('from_date')
            ? Carbon::parse($request->input('from_date'))->startOfDay()
            : Carbon::now()->subDays(7)->startOfDay();

        $to = $request->filled('to_date')
            ? Carbon::parse($request->input('to_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        return [$from, $to];
    }
}
