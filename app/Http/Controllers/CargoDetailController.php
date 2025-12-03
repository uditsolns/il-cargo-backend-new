<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Mail\InspectionReportMail;
use App\Models\CargoDetail;
use App\Models\PhaseZone;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class CargoDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index2()
    {

        $cargoDetails = CargoDetail::with('photographs', 'checklists', 'group', 'group.phases', 'photographs.zone', 'photographs.phase')
            ->orderByDesc('id')
            ->get();


        return response()->json(['cargo_details' => $cargoDetails]);
    }

    public function index()
    {
        $user = Auth::user();
        // Check if the user is an admin
        if ($user->is_admin == 1) {
            // If the user is an admin, show all cargo details
            $cargoDetails = CargoDetail::with('photographs', 'checklists', 'group', 'photographs.zone', 'photographs.phase', 'consignee')
                ->orderByDesc('id')
                ->get();
        } elseif ($user->user_status == 1) {
            $cargoDetails = CargoDetail::with('photographs', 'checklists', 'group', 'consignee')
                ->where('channel_partner_id', $user->channel_partner_id)
                ->orderByDesc('id')
                ->get();
        } elseif ($user->role == "Insured's Dispatch Supervisor" || $user->role == "Insured's Representative") {
            $cargoDetails = CargoDetail::with('photographs', 'checklists', 'group', 'consignee')
                ->where('group_id', $user->group_id)
                ->orderByDesc('id')
                ->get();
        } elseif ($user->role == 'Channel Partner') {
            // If the user is a channel partner, retrieve cargo details based on their group's parent_user_id
            $cargoDetails = CargoDetail::with('photographs', 'checklists', 'group', 'consignee')
                ->where('channel_partner_id', $user->id)
                ->orderByDesc('id')
                ->get();
        } elseif ($user->role == 'Consignee') {
            $cargoDetails = CargoDetail::with(['photographs', 'checklists', 'group', 'consignee', 'group.phases', 'photographs.zone', 'photographs.phase', 'consignee'])
                ->where('consignee_id', $user->id)
                ->orderByDesc('id')
                ->get();
        } else {
            // dd($user);
            // For simple users, show cargo details associated with their user ID
            $cargoDetails = CargoDetail::with('photographs', 'checklists', 'group.phases', 'photographs.zone', 'photographs.phase', 'consignee')
                ->where('group_id', $user->group_id)
                ->orderByDesc('id')
                ->get();
        }

        return response()->json(['cargo_details' => $cargoDetails]);
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
        if ($user->role == 'Channel Partner') {
            // user id
            $userId = $user->id;
            //
            $channel_partner_id = $user->id;
            $groupId = $request->group_id ?? '';
        } elseif ($user->user_status == 1) {
            // user id
            $userId = $user->id;
            //
            $channel_partner_id = $user->channel_partner_id;
        } else {
            // user id
            $userId = $user->id;
            //
            $groupId = $request->group_id;
        }
        // Generate dispatch ID using the Helper class
        $dispatchId = Helper::generateDispatchId();
        // Check and handle file uploads
        $invoiceFilePath = $request->file('invoice') ? $request->file('invoice')->store('public/invoices') : null;
        $invoiceFileName = $invoiceFilePath ? basename($invoiceFilePath) : null;

        $licenseFilePath = $request->file('driver_lic_no') ? $request->file('driver_lic_no')->store('public/license') : null;
        $licenseFileName = $licenseFilePath ? basename($licenseFilePath) : null;

        $packingListFilePath = $request->file('packing_list') ? $request->file('packing_list')->store('public/packing_list') : null;
        $packingListFileName = $packingListFilePath ? basename($packingListFilePath) : null;

        $vehicleFitnessFilePath = $request->file('veh_fitness_cert') ? $request->file('veh_fitness_cert')->store('public/veh_fitness_cert') : null;
        $vehicleFitnessFileName = $vehicleFitnessFilePath ? basename($vehicleFitnessFilePath) : null;

        $consignee_id = null;

        if ($request->consignee_email && $request->consignee_phone) {
            $consignee = User::where('email', $request->consignee_email)->first();

            if (!$consignee) {
                $consignee = User::create([
                    'name' => $request->consignee_name or null,
                    'email' => $request->consignee_email,
                    'phone' => $request->consignee_phone,
                    'password' => bcrypt($request->consignee_phone),
                    'role' => 'Consignee',
                    'is_admin' => 0,
                ]);

                $phaseId = 29;
                $zoneId = 46;

                $phaseZone = PhaseZone::where('user_id', $consignee->id)
                    ->where('phase_id', $phaseId)
                    ->where('zone_id', $zoneId)
                    ->first();

                if (!$phaseZone) {
                    PhaseZone::create([
                        'user_id' => $consignee->id,
                        'phase_id' => $phaseId,
                        'zone_id' => $zoneId,
                    ]);
                }


            }

            $consignee_id = $consignee->id;
        }

        // Create or update CargoDetail record
        $cargoDetail = CargoDetail::UpdateOrCreate(
            ['cargo_unit_serial_no' => $request->input('cargo_unit_serial_no')],
            [
                'veh_reg_no' => $request->input('veh_reg_no'),
                'invoice' => $invoiceFileName,
                'driver_lic_no' => $licenseFileName,
                'packing_list' => $packingListFileName,
                'veh_fitness_cert' => $vehicleFitnessFileName,
                'serial_no' => $request->input('serial_no'),
                'invoice_value' => $request->input('invoice_value'),
                'dispatch_lat' => $request->input('dispatch_lat'),
                'dispatch_long' => $request->input('dispatch_long'),
                'destination_long' => $request->input('destination_long'),
                'destination_lat' => $request->input('destination_lat'),
                'value_add' => $request->input('value_add'),
                'date_transit' => $request->input('date_transit'),
                'veh_carrying_capacity' => $request->input('veh_carrying_capacity'),
                'pending_servey' => $request->input('pending_servey'),
                'address' => $request->input('address'),
                'dispatch_type' => $request->input('dispatch_type'),
                'flat_track_number' => $request->input('flat_track_number'),
                'destination_pin' => $request->input('destination_pin'),
                'origin_pin' => $request->input('origin_pin'),
                'group_id' => $groupId ?? null,
                'destination_address' => $request->input('destination_address'),
                'dispatch_id' => $dispatchId,
                'user_id' => $userId,
                'channel_partner_id' => isset($channel_partner_id) ? $channel_partner_id : null,
                'consignee_id' => $consignee_id,
                'remarks' => $request->input('remarks') ?? null,
            ]
        );

        // Retrieve the updated or inserted CargoDetail
        $cargoDetail = CargoDetail::where('cargo_unit_serial_no', $request->input('cargo_unit_serial_no'))->first();

        return response()->json(['cargo_detail' => $cargoDetail], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cargoDetail = CargoDetail::with('photographs', 'checklists', 'group.phases', 'photographs.zone', 'photographs.phase')->findOrFail($id);

        return response()->json(['cargo_details' => $cargoDetail]);

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

        return response()->json(['count' => $count]);
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
        if ($user->role == 'Channel Partner') {
            $groupId = $request->group_id;
        }

        // Generate dispatch ID using the Helper class
        // $dispatchId = Helper::generateDispatchId();

        // Handle file uploads and get file names
        $invoiceFileName = $request->hasFile('invoice') ? basename($request->file('invoice')->store('public/invoices')) : null;
        $licenseFileName = $request->hasFile('driver_lic_no') ? basename($request->file('driver_lic_no')->store('public/license')) : null;
        $packingListFileName = $request->hasFile('packing_list') ? basename($request->file('packing_list')->store('public/packing_list')) : null;
        $vehicleFitnessFileName = $request->hasFile('veh_fitness_cert') ? basename($request->file('veh_fitness_cert')->store('public/veh_fitness_cert')) : null;

        // Prepare data for CargoDetail record
        $data = [
            'veh_reg_no' => $request->input('veh_reg_no'),
            'serial_no' => $request->input('serial_no'),
            'invoice_value' => $request->input('invoice_value'),
            'dispatch_lat' => $request->input('dispatch_lat'),
            'dispatch_long' => $request->input('dispatch_long'),
            'destination_long' => $request->input('destination_long'),
            'destination_lat' => $request->input('destination_lat'),
            'value_add' => $request->input('value_add'),
            'date_transit' => $request->input('date_transit'),
            'veh_carrying_capacity' => $request->input('veh_carrying_capacity'),
            'pending_servey' => $request->input('pending_servey'),
            'address' => $request->input('address'),
            'dispatch_type' => $request->input('dispatch_type'),
            'flat_track_number' => $request->input('flat_track_number'),
            'destination_pin' => $request->input('destination_pin'),
            'origin_pin' => $request->input('origin_pin'),
            'group_id' => $groupId,
            'destination_address' => $request->input('destination_address'),
            // 'dispatch_id' => $dispatchId,
            'remarks' => $request->input('remarks') ?? null,
        ];

        // Add file names to data array if they are not null
        if ($invoiceFileName !== null) {
            $data['invoice'] = $invoiceFileName;
        }
        if ($licenseFileName !== null) {
            $data['driver_lic_no'] = $licenseFileName;
        }
        if ($packingListFileName !== null) {
            $data['packing_list'] = $packingListFileName;
        }
        if ($vehicleFitnessFileName !== null) {
            $data['veh_fitness_cert'] = $vehicleFitnessFileName;
        }

        // Remove null values from the data array
        $data = array_filter($data, function ($value) {
            return $value !== null;
        });

        // Create or update CargoDetail record
        $cargoDetail = CargoDetail::updateOrCreate(
            ['cargo_unit_serial_no' => $request->input('cargo_unit_serial_no')],
            $data
        );

        // Retrieve the updated or inserted CargoDetail
        $cargoDetail = CargoDetail::where('cargo_unit_serial_no', $request->input('cargo_unit_serial_no'))->first();

        $cargoDetail->consignee()->update([
            'email' => $request->input('consignee_email') ?? $cargoDetail->consignee->email,
            'phone' => $request->input('consignee_phone')  ?? $cargoDetail->consignee->phone,
        ]);

        if ($cargoDetail && $cargoDetail->pending_survey == 1) {
            // sending inspection mail
            Mail::to('udit9solutions@gmail.com')->send(new InspectionReportMail($cargoDetail));
        }

        return response()->json(['cargo_detail' => $cargoDetail], 201);
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
}
