<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $search = $request->query("search");
        $perPage = $request->query("perPage") ?? 10;

        $customers = Customer::with("sops")
            ->search($search)
            ->latest()->paginate($perPage);

        return response()->json($customers);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        return Customer::query()->create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer) {
        return $customer->loadMissing("sops");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer) {
        $customer->update($request->all());
        return $customer;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer) {
        return $customer->delete();
    }
}
