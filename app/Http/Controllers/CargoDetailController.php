<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Mail\InspectionReportMail;
use App\Models\CargoDetail;
use App\Models\PhaseZone;
use App\Models\User;
use App\Services\VideoWatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CargoDetailController extends Controller
{
    public function __construct(private VideoWatchService $videoWatchService) {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index2()
    {
        $cargoDetails = CargoDetail::with(
            "photographs",
            "checklists",
            "group",
            "group.phases",
            "photographs.zone",
            "photographs.phase",
        )
            ->orderByDesc("id")
            ->get();

        return response()->json(["cargo_details" => $cargoDetails]);
    }

    public function index(Request $request)
    {
        $perPage = $request->input("per_page", 15);
        $search = $request->input("search");
        $onlyPending = $request->input("only_pending", false);
        $user = Auth::user();

        $baseRelations = ["photographs", "checklists", "group", "consignee"];
        $query = CargoDetail::with($baseRelations)->orderByDesc("id");

        // Search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("cargo_unit_serial_no", "like", "%{$search}%")
                    ->orWhere("veh_reg_no", "like", "%{$search}%")
                    ->orWhere("serial_no", "like", "%{$search}%");
            });
        }

        if ($onlyPending) {
            $query->where(function ($q) {
                $q->whereNull("pending_servey")->orWhere("pending_servey", 0);
            });
        }

        // Admin: see everything
        if ($user->is_admin == 1) {
            $query->with(["photographs.zone", "photographs.phase"]);
        }
        // Channel partner status user
        elseif ($user->user_status == 1) {
            $query->where("channel_partner_id", $user->channel_partner_id);
        }
        // Role-based filtering
        elseif (
            in_array($user->role, [
                "Insured's Dispatch Supervisor",
                "Insured's Representative",
            ])
        ) {
            $query->where("group_id", $user->group_id);
        } elseif ($user->role == "Channel Partner") {
            $query->where("channel_partner_id", $user->id);
        } elseif ($user->role == "Consignee") {
            $query
                ->with([
                    "group.phases",
                    "photographs.zone",
                    "photographs.phase",
                ])
                ->where("consignee_id", $user->id);
        }
        // Regular users
        else {
            $query
                ->with([
                    "group.phases",
                    "photographs.zone",
                    "photographs.phase",
                ])
                ->where("group_id", $user->group_id);
        }

        return response()->json($query->paginate($perPage));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role == "Channel Partner") {
            $userId = $user->id;
            $channel_partner_id = $user->id;
            $groupId = $request->group_id ?? "";
        } elseif ($user->user_status == 1) {
            $userId = $user->id;
            $channel_partner_id = $user->channel_partner_id;
        } else {
            $userId = $user->id;
            $groupId = $request->group_id;
        }

        // NEW — validate the driver / video inputs before doing any writes
        $fields = Validator::make($request->all(), [
            "driver_name" => "nullable|string",
            "driver_email" => "nullable|email",
            "driver_mobile_no" => "nullable|string",
            "video_tutorial_ids" => "nullable|array",
            "video_tutorial_ids.*" => "exists:video_tutorials,id",
        ]);

        if ($fields->fails()) {
            return response()->json(["error" => $fields->errors()], 422);
        }

        $dispatchId = Helper::generateDispatchId();

        $invoiceFilePath = $request->file("invoice")
            ? $request->file("invoice")->store("public/invoices")
            : null;
        $invoiceFileName = $invoiceFilePath ? basename($invoiceFilePath) : null;

        $licenseFilePath = $request->file("driver_lic_no")
            ? $request->file("driver_lic_no")->store("public/license")
            : null;
        $licenseFileName = $licenseFilePath ? basename($licenseFilePath) : null;

        $packingListFilePath = $request->file("packing_list")
            ? $request->file("packing_list")->store("public/packing_list")
            : null;
        $packingListFileName = $packingListFilePath
            ? basename($packingListFilePath)
            : null;

        $vehicleFitnessFilePath = $request->file("veh_fitness_cert")
            ? $request
                ->file("veh_fitness_cert")
                ->store("public/veh_fitness_cert")
            : null;
        $vehicleFitnessFileName = $vehicleFitnessFilePath
            ? basename($vehicleFitnessFilePath)
            : null;

        $consignee_id = null;

        if ($request->consignee_email && $request->consignee_phone) {
            $consignee = User::where(
                "email",
                $request->consignee_email,
            )->first();

            if (!$consignee) {
                $consignee = User::create([
                    "name" => $request->consignee_name or null,
                    "email" => $request->consignee_email,
                    "phone" => $request->consignee_phone,
                    "password" => bcrypt($request->consignee_phone),
                    "role" => "Consignee",
                    "is_admin" => 0,
                ]);

                $phaseId = 29;
                $zoneId = 46;

                $phaseZone = PhaseZone::where("user_id", $consignee->id)
                    ->where("phase_id", $phaseId)
                    ->where("zone_id", $zoneId)
                    ->first();

                if (!$phaseZone) {
                    PhaseZone::create([
                        "user_id" => $consignee->id,
                        "phase_id" => $phaseId,
                        "zone_id" => $zoneId,
                    ]);
                }
            }

            $consignee_id = $consignee->id;
        }

        // NEW — driver auto-provisioning (find-or-create), same mechanism as consignee above
        $driver = $this->videoWatchService->findOrCreateDriver(
            $request->input("driver_name"),
            $request->input("driver_email"),
            $request->input("driver_mobile_no"),
        );

        $cargoDetail = CargoDetail::UpdateOrCreate(
            ["cargo_unit_serial_no" => $request->input("cargo_unit_serial_no")],
            [
                "veh_reg_no" => $request->input("veh_reg_no"),
                "invoice" => $invoiceFileName,
                "driver_lic_no" => $licenseFileName,
                "packing_list" => $packingListFileName,
                "veh_fitness_cert" => $vehicleFitnessFileName,
                "serial_no" => $request->input("serial_no"),
                "invoice_value" => $request->input("invoice_value"),
                "dispatch_lat" => $request->input("dispatch_lat"),
                "dispatch_long" => $request->input("dispatch_long"),
                "destination_long" => $request->input("destination_long"),
                "destination_lat" => $request->input("destination_lat"),
                "value_add" => $request->input("value_add"),
                "date_transit" => $request->input("date_transit"),
                "veh_carrying_capacity" => $request->input(
                    "veh_carrying_capacity",
                ),
                "pending_servey" => $request->input("pending_servey"),
                "address" => $request->input("address"),
                "dispatch_type" => $request->input("dispatch_type"),
                "flat_track_number" => $request->input("flat_track_number"),
                "destination_pin" => $request->input("destination_pin"),
                "origin_pin" => $request->input("origin_pin"),
                "group_id" => $groupId ?? null,
                "destination_address" => $request->input("destination_address"),
                "dispatch_id" => $dispatchId,
                "user_id" => $userId,
                "channel_partner_id" => isset($channel_partner_id)
                    ? $channel_partner_id
                    : null,
                "consignee_id" => $consignee_id,
                "remarks" => $request->input("remarks") ?? null,
                "dl_no" => $request->input("dl_no"),
                "dl_dob" => $request->input("dl_dob"),
                "driver_aadhaar_no" => $request->input("driver_aadhaar_no"),
                "is_rc_verified" => $request->input("is_rc_verified"),
                "is_dl_verified" => $request->input("is_dl_verified"),
                "is_aadhaar_verified" => $request->input("is_aadhaar_verified"),
                "is_verification_done" => $request->input(
                    "is_verification_done",
                ),
                // NEW
                "driver_id" => $driver?->id,
                "driver_email" => $request->input("driver_email"),
                "driver_mobile_no" => $request->input("driver_mobile_no"),
            ],
        );

        $cargoDetail = CargoDetail::where(
            "cargo_unit_serial_no",
            $request->input("cargo_unit_serial_no"),
        )->first();

        // NEW — assign applicable tutorial videos to this trip, then compute driver_videos_status
        if ($request->has("video_tutorial_ids")) {
            $this->videoWatchService->syncTripVideos(
                $cargoDetail,
                $request->input("video_tutorial_ids", []),
            );
            $cargoDetail = $cargoDetail->fresh();
        }

        return response()->json(["cargo_detail" => $cargoDetail], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cargoDetail = CargoDetail::with(
            "photographs",
            "checklists",
            "group.phases",
            "photographs.zone",
            "photographs.phase",
        )->findOrFail($id);

        return response()->json(["cargo_details" => $cargoDetail]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function count()
    {
        $count = CargoDetail::count();

        return response()->json(["count" => $count]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Determine the group ID based on the user's role
        $groupId = null;
        if ($user->role == "Channel Partner") {
            $groupId = $request->group_id;
        }

        $fields = Validator::make($request->all(), [
            "driver_name" => "nullable|string",
            "driver_email" => "nullable|email",
            "driver_mobile_no" => "nullable|string",
            "video_tutorial_ids" => "nullable|array",
            "video_tutorial_ids.*" => "exists:video_tutorials,id",
        ]);

        // Generate dispatch ID using the Helper class
        // $dispatchId = Helper::generateDispatchId();

        // Handle file uploads and get file names
        $monthFolder = now()->format("Y-m");

        $invoiceFileName = $request->hasFile("invoice")
            ? $monthFolder .
                "/" .
                basename(
                    $request
                        ->file("invoice")
                        ->store("public/invoices/" . $monthFolder),
                )
            : null;
        $licenseFileName = $request->hasFile("driver_lic_no")
            ? $monthFolder .
                "/" .
                basename(
                    $request
                        ->file("driver_lic_no")
                        ->store("public/license/" . $monthFolder),
                )
            : null;
        $packingListFileName = $request->hasFile("packing_list")
            ? $monthFolder .
                "/" .
                basename(
                    $request
                        ->file("packing_list")
                        ->store("public/packing_list/" . $monthFolder),
                )
            : null;
        $vehicleFitnessFileName = $request->hasFile("veh_fitness_cert")
            ? $monthFolder .
                "/" .
                basename(
                    $request
                        ->file("veh_fitness_cert")
                        ->store("public/veh_fitness_cert/" . $monthFolder),
                )
            : null;

        // Prepare data for CargoDetail record
        $data = [
            "veh_reg_no" => $request->input("veh_reg_no"),
            "serial_no" => $request->input("serial_no"),
            "invoice_value" => $request->input("invoice_value"),
            "dispatch_lat" => $request->input("dispatch_lat"),
            "dispatch_long" => $request->input("dispatch_long"),
            "destination_long" => $request->input("destination_long"),
            "destination_lat" => $request->input("destination_lat"),
            "value_add" => $request->input("value_add"),
            "date_transit" => $request->input("date_transit"),
            "veh_carrying_capacity" => $request->input("veh_carrying_capacity"),
            "pending_servey" => $request->input("pending_servey"),
            "address" => $request->input("address"),
            "dispatch_type" => $request->input("dispatch_type"),
            "flat_track_number" => $request->input("flat_track_number"),
            "destination_pin" => $request->input("destination_pin"),
            "origin_pin" => $request->input("origin_pin"),
            "group_id" => $groupId,
            "destination_address" => $request->input("destination_address"),
            // 'dispatch_id' => $dispatchId,
            "remarks" => $request->input("remarks") ?? null,
        ];

        // Add file names to data array if they are not null
        if ($invoiceFileName !== null) {
            $data["invoice"] = $invoiceFileName;
        }
        if ($licenseFileName !== null) {
            $data["driver_lic_no"] = $licenseFileName;
        }
        if ($packingListFileName !== null) {
            $data["packing_list"] = $packingListFileName;
        }
        if ($vehicleFitnessFileName !== null) {
            $data["veh_fitness_cert"] = $vehicleFitnessFileName;
        }

        // Remove null values from the data array
        $data = array_filter($data, function ($value) {
            return $value !== null;
        });

        // Create or update CargoDetail record
        $cargoDetail = CargoDetail::updateOrCreate(
            ["cargo_unit_serial_no" => $request->input("cargo_unit_serial_no")],
            $data,
        );

        // Retrieve the updated or inserted CargoDetail
        $cargoDetail = CargoDetail::where(
            "cargo_unit_serial_no",
            $request->input("cargo_unit_serial_no"),
        )->first();

        if (
            $request->filled("driver_email") ||
            $request->filled("driver_mobile_no")
        ) {
            $driver = $this->videoWatchService->findOrCreateDriver(
                $request->input("driver_name"),
                $request->input("driver_email"),
                $request->input("driver_mobile_no"),
            );

            $cargoDetail->update([
                "driver_id" => $driver?->id,
                "driver_email" => $request->input("driver_email"),
                "driver_mobile_no" => $request->input("driver_mobile_no"),
            ]);
        }

        if ($request->has("video_tutorial_ids")) {
            $this->videoWatchService->syncTripVideos(
                $cargoDetail,
                $request->input("video_tutorial_ids", []),
            );
        }

        $cargoDetail = $cargoDetail->fresh();

        if ($cargoDetail->consignee) {
            $cargoDetail->consignee()->update([
                "email" =>
                    $request->input("consignee_email") ??
                    $cargoDetail->consignee->email,
                "phone" =>
                    $request->input("consignee_phone") ??
                    $cargoDetail->consignee->phone,
            ]);
        }

        if (
            $request->filled("driver_email") ||
            $request->filled("driver_mobile_no")
        ) {
            $driver = $this->videoWatchService->findOrCreateDriver(
                $request->input("driver_name"),
                $request->input("driver_email"),
                $request->input("driver_mobile_no"),
            );

            $cargoDetail->update([
                "driver_id" => $driver?->id,
                "driver_email" => $request->input("driver_email"),
                "driver_mobile_no" => $request->input("driver_mobile_no"),
            ]);
        }

        if ($request->has("video_tutorial_ids")) {
            $this->videoWatchService->syncTripVideos(
                $cargoDetail,
                $request->input("video_tutorial_ids", []),
            );
        } elseif ($cargoDetail->driver_id) {
            // driver may have changed even if the video list didn't — recompute status either way
            $this->videoWatchService->recalculateStatus($cargoDetail);
        }

        $cargoDetail = $cargoDetail->fresh();

        if ($cargoDetail && $cargoDetail->pending_survey == 1) {
            // sending inspection mail
            Mail::to("udit9solutions@gmail.com")->send(
                new InspectionReportMail($cargoDetail),
            );
        }

        return response()->json(["cargo_detail" => $cargoDetail], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // dd($id);
        $cargoDetail = CargoDetail::findOrFail($id);
        // dd($car)
        $cargoDetail->delete();
        return response()->json("Deleted", 200);
    }

    public function report(CargoDetail $cargoDetail)
    {
        $pdf = (new InspectionReportMail($cargoDetail))->generatePdfReport();

        return $pdf->stream("{$cargoDetail->cargo_unit_serial_no}-report.pdf");
    }

    public function export(Request $request)
    {
        $request->validate([
            "from_date" => ["required", "date", "before_or_equal:to_date"],
            "to_date" => ["required", "date", "after_or_equal:from_date"],
        ]);

        $fromDate = $request->input("from_date");
        $toDate = $request->input("to_date");
        $groupId = $request->input("group_id");
        $user = Auth::user();

        $baseRelations = ["group", "consignee"];
        $query = CargoDetail::with($baseRelations)->orderByDesc("id");

        // Date range filtering
        if ($fromDate && $toDate) {
            $query
                ->whereDate("created_at", ">=", $fromDate)
                ->whereDate("created_at", "<=", $toDate);
        }

        // Admin: see everything
        if ($user->is_admin == 1) {
            if ($groupId) {
                $query->where("group_id", $groupId);
            }
        }
        // Channel partner status user
        elseif ($user->user_status == 1) {
            $query->where("channel_partner_id", $user->channel_partner_id);
        }
        // Role-based filtering
        elseif (
            in_array($user->role, [
                "Insured's Dispatch Supervisor",
                "Insured's Representative",
            ])
        ) {
            $query->where("group_id", $user->group_id);
        } elseif ($user->role == "Channel Partner") {
            $query->where("channel_partner_id", $user->id);
            // Filter by group_id if provided
            if ($groupId) {
                $query->where("group_id", $groupId);
            }
        } elseif ($user->role == "Consignee") {
            $query->where("consignee_id", $user->id);
        }

        return response()->json(["cargo_details" => $query->get()]);
    }
}
