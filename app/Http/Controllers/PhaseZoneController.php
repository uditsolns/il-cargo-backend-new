<?php

namespace App\Http\Controllers;

use App\Models\PhaseZone;
use Illuminate\Http\Request;

class PhaseZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PhaseZone::all();
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

}


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PhaseZone  $email
     * @return \Illuminate\Http\Response
     */
    public function show(PhaseZone $email)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PhaseZone  $email
     * @return \Illuminate\Http\Response
     */
    public function edit(PhaseZone $email)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PhaseZone  $email
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PhaseZone $email)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PhaseZone  $email
     * @return \Illuminate\Http\Response
     */
    public function destroy(PhaseZone $email)
    {
        //
    }
}
