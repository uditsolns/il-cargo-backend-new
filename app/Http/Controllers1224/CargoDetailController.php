<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CargoDetail;
use Illuminate\Support\Facades\Validator;

class CargoDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cargoDetails = CargoDetail::all();
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required', // Replace 'trips' with the actual table name
            'veh_reg_no' => 'required|max:255',
            'cargo_unit_serial_no' => 'required|max:20',
            'driver_lic_no' => 'required|max:20',
            // 'veh_fitness_cert' => 'json|max:255',
            // 'veh_carrying_capacity' => 'json|max:255',
            'invoice' => 'required|max:20',
            'packing_list' => 'required|max:20',
            'serial_no' => 'required|max:20',
            'invoice_value' => 'required|max:20',
            'dispatch_lat' => 'required|numeric',
            'dispatch_long' => 'required|numeric',
            'destination_long' => 'required|numeric',
            'destination_lat' => 'required|numeric',
            // Add validation for other fields
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 403);
        }
        $cargoDetail = CargoDetail::create($request->all());
        return response()->json(['cargo_detail' => $cargoDetail], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cargoDetail = CargoDetail::findOrFail($id);
        return response()->json(['cargo_detail' => $cargoDetail]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $cargoDetail = CargoDetail::findOrFail($id);

        // $request->validate([
        //     'trip_id' => 'required|exists:trips,id', // Replace 'trips' with the actual table name
        //     // 'veh_reg_no' => 'required|max:255',
        //     'cargo_unit_serial_no' => 'required|max:20',
        //     'driver_lic_no' => 'required|max:20',
        //     'veh_fitness_cert' => 'json|max:255',
        //     'veh_carrying_capacity' => 'json|max:255',
        //     'invoice' => 'required|max:20',
        //     'packing_list' => 'required|max:20',
        //     'serial_no' => 'required|max:20',
        //     'invoice_value' => 'required|max:20',
        //     'dispatch_lat' => 'required|numeric',
        //     'dispatch_long' => 'required|numeric',
        //     'destination_long' => 'required|numeric',
        //     'destination_lat' => 'required|numeric',
        //     // Add validation for other fields
        // ]);

        $cargoDetail->update($request->all());
        return response()->json(['cargo_detail' => $cargoDetail]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cargoDetail = CargoDetail::findOrFail($id);
        $cargoDetail->delete();
        return response()->json(1, 204);
    }
}
