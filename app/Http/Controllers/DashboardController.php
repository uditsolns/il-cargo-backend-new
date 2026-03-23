<?php

namespace App\Http\Controllers;

use App\Models\ApiUsageLog;
use App\Models\CargoDetail;
use App\Models\Group;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        // Default to last 30 days
        $fromDate = $request->from_date
            ? Carbon::parse($request->from_date)->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();

        $toDate = $request->to_date
            ? Carbon::parse($request->to_date)->endOfDay()
            : Carbon::now()->endOfDay();

        // Apply date filter to base queries
        $dateFilter = function ($query) use ($fromDate, $toDate) {
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        };

        return response()->json([
            'success' => true,
            'data' => [
                'counts' => $this->getCounts(),
                'graphs' => $this->getGraphData($fromDate, $toDate),
                'inspection_summary' => $this->getInspectionSummary($dateFilter),
            ],
            'filters' => [
                'from_date' => $fromDate->format('Y-m-d'),
                'to_date' => $toDate->format('Y-m-d'),
            ]
        ]);
    }

    private function getCounts()
    {
        // Count customers (groups)
        $customersCount = Group::query()
            ->count();

        // Count users
        $usersCount = User::query()
            ->count();

        // Count dispatches (cargo details)
        $dispatchCount = CargoDetail::query()
            ->count();

        return [
            'customers' => $customersCount,
            'users' => $usersCount,
            'dispatches' => $dispatchCount,
            'apis' => ApiUsageLog::count(),
        ];
    }

    private function getGraphData($fromDate, $toDate)
    {
        // Create date range
        $period = CarbonPeriod::create($fromDate, '1 day', $toDate);
        $dateRange = [];
        foreach ($period as $date) {
            $dateRange[$date->format('Y-m-d')] = 0;
        }

        // Users graph data
        $usersData = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            // ->whereBetween('created_at', [$fromDate, $toDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Customers (groups) graph data
        $customersData = Group::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            // ->whereBetween('created_at', [$fromDate, $toDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        $cargoDetailsData = CargoDetail::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )-> whereBetween('created_at', [$fromDate, $toDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        $invoiceValueData = CargoDetail::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(CAST(invoice_value as DECIMAL(15,2))) as total_value')
        )
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->whereNotNull('invoice_value')
            ->where('invoice_value', '!=', '')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total_value', 'date')
            ->toArray();

        // Merge with date range to fill missing dates with 0
        $usersGraph = array_replace($dateRange, $usersData);
        $customersGraph = array_replace($dateRange, $customersData);
        $cargoDetailsGraph = array_replace($dateRange, $cargoDetailsData);
        $invoiceValueGraph = array_replace($dateRange, $invoiceValueData);

        // Format for frontend
        return [
            'users' => array_map(function ($date, $count) {
                return [
                    'date' => $date,
                    'count' => $count
                ];
            }, array_keys($usersGraph), $usersGraph),
            'customers' => array_map(function ($date, $count) {
                return [
                    'date' => $date,
                    'count' => $count
                ];
            }, array_keys($customersGraph), $customersGraph),
            'cargo_details' => array_map(function ($date, $count) {
                return [
                    'date' => $date,
                    'count' => $count
                ];
            }, array_keys($cargoDetailsGraph), $cargoDetailsGraph),
            'invoice_value' => array_map(function ($date, $value) {
                return [
                    'date' => $date,
                    'value' => $value ?? 0
                ];
            }, array_keys($invoiceValueGraph), $invoiceValueGraph),
        ];
    }

    private function getInspectionSummary($dateFilter)
    {
        // Get all cargo details with checklists
        $cargoDetails = CargoDetail::with('checklists')
            ->when($dateFilter, $dateFilter)
            ->get();

        $complianceRateGroups = [];
        $totalInspections = 0;

        foreach ($cargoDetails as $cargo) {
            $checklists = $cargo->checklists ?? collect();
            $totalChecks = $checklists->count();

            if ($totalChecks === 0) {
                continue;
            }

            $totalInspections++;

            $breachedCount = $checklists->where('is_sop_breached', 'yes')->count();
            $compliantCount = $totalChecks - $breachedCount;
            $complianceRate = round(($compliantCount / $totalChecks) * 100, 1);

            // Group by compliance rate
            if (!isset($complianceRateGroups[$complianceRate])) {
                $complianceRateGroups[$complianceRate] = 0;
            }
            $complianceRateGroups[$complianceRate]++;
        }

        // Sort by compliance rate
        ksort($complianceRateGroups);

        // Format for pie chart
        $pieChartData = [];
        foreach ($complianceRateGroups as $rate => $count) {
            $percentage = $totalInspections > 0
                ? round(($count / $totalInspections) * 100, 1)
                : 0;

            $pieChartData[] = [
                'compliance_rate' => $rate,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        return [
            'total_inspections' => $totalInspections,
            'compliance_data' => $pieChartData,
        ];
    }

    public function filterByCustomer(Request $request)
    {
        $request->validate([
            'group_id' => 'nullable|exists:groups,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        // Default to last 30 days
        $fromDate = $request->from_date
            ? Carbon::parse($request->from_date)->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();

        $toDate = $request->to_date
            ? Carbon::parse($request->to_date)->endOfDay()
            : Carbon::now()->endOfDay();

        // If no group_id provided, get the customer with most dispatches in last 30 days
        if (!$request->group_id) {
            $groupId = CargoDetail::select('group_id', DB::raw('COUNT(*) as dispatch_count'))
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->whereNotNull('group_id')
                ->groupBy('group_id')
                ->orderByDesc('dispatch_count')
                ->value('group_id');

            // If no dispatches found in last 30 days, return error
            if (!$groupId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No customer found with dispatches in the last 30 days'
                ], 404);
            }
        } else {
            $groupId = $request->group_id;
        }


        // Get cargo details for this customer with date filter
        $cargoDetails = CargoDetail::where('group_id', $groupId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->with(['checklists', 'photographs'])
            ->get();

        // Calculate compliance statistics
        $totalDispatches = $cargoDetails->count();
        $totalCompliance = 0;
        $inCompliance = 0;
        $outOfCompliance = 0;
        $totalInvoiceValue = 0;

        foreach ($cargoDetails as $cargo) {
            // Calculate invoice value
            if (!empty($cargo->invoice_value)) {
                $totalInvoiceValue += floatval($cargo->invoice_value);
            }

            // Calculate compliance
            $checklists = $cargo->checklists ?? collect();
            $totalChecks = $checklists->count();
            $totalCompliance += $totalChecks;

            if ($totalChecks > 0) {
                $breachedCount = $checklists->where('is_sop_breached', 'yes')->count();
                $compliantCount = $totalChecks - $breachedCount;
//                $complianceRate = ($compliantCount / $totalChecks) * 100;

                $inCompliance += $compliantCount;
                $outOfCompliance += $breachedCount;

                // Consider 80% or above as in compliance
//                if ($complianceRate >= 80) {
//                    $inCompliance++;
//                } else {
//                    $outOfCompliance++;
//                }
            }
        }

        // Get total photographs
        $totalPhotographs = DB::table('photographs')
            ->join('cargo_details', 'photographs.cargo_id', '=', 'cargo_details.id')
            ->where('cargo_details.group_id', $groupId)
            ->whereBetween('cargo_details.created_at', [$fromDate, $toDate])
            ->whereNull('photographs.deleted_at')
            ->count();

        // Get total zones for this group
        $totalZones = DB::table('zones')
            ->where('group_id', $groupId)
            ->whereNull('deleted_at')
            ->distinct('id')
            ->count();

        // Get total phases for this group
        $totalPhases = DB::table('phases')
            ->where('group_id', $groupId)
            ->whereNull('deleted_at')
            ->distinct('id')
            ->count();

        // Get customer details
        $customer = Group::find($groupId);

        return response()->json([
            'success' => true,
            'data' => [
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'city' => $customer->city,
                    'gst' => $customer->gst,
                ],
                'statistics' => [
                    'total_dispatches' => $totalDispatches,
                    'total_compliance' => $totalCompliance,
                    'in_compliance' => $inCompliance,
                    'out_of_compliance' => $outOfCompliance,
                    'compliance_rate' => $totalDispatches > 0
                        ? round(($inCompliance / $totalCompliance) * 100, 1)
                        : 0,
                    'total_invoice_value' => round($totalInvoiceValue, 2),
                    'total_photographs' => $totalPhotographs,
                    'total_zones' => $totalZones,
                    'total_phases' => $totalPhases,
                ],
                'filters' => [
                    'from_date' => $fromDate->format('Y-m-d'),
                    'to_date' => $toDate->format('Y-m-d'),
                ]
            ]
        ]);
    }
}
