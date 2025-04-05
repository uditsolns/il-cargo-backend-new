<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sop;
use App\Traits\HasStoreFile;
use Illuminate\Http\Request;

class SopController extends Controller
{
    use HasStoreFile;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $perPage = $request->query("perPage") ?? 10;
        return Sop::query()->with("customer")->latest()->paginate($perPage);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $request->validate([
            "pdf" => "required|file|mimes:pdf",
            "expiry_date" => "required|date",
            "customer_id" => "required",
        ]);

        return $this->createOrUpdateSop($request);
    }

    public function createOrUpdateSop(Request $request, Sop $sop = null) {
        if(!isset($sop)) {
            $sop = new Sop();
        }
        $sop->fill($request->except("pdf"));

        if ($request->hasFile("pdf")) {
            $sop->pdf = $this->storeFile($request, "pdf", "sops");
        }

        $sop->save();
        return $sop;
    }

    /**
     * Display the specified resource.
     */
    public function show(Sop $sop) {
        return $sop->loadMissing("customer");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sop $sop) {
        $request->validate([
            "pdf" => "sometimes|mimes:pdf",
            "customer_id" => "sometimes",
            "expiry_date" => "sometimes|date",
        ]);
        return $this->createOrUpdateSop($request, $sop);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sop $sop) {
        return $sop->delete();
    }
}
