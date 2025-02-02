<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgreementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Agreements/Index', [
            'agreements' => Agreement::with('customer')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Agreements/Create',[
            'customers' => Customer::all(),
            'agreement_max' => (Agreement::max('agreement_no')??0) + 1,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'agreement_no' => 'required',
            'date' => 'required',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'customer_id' => 'required',
        ]);
        Agreement::create($request->all());
        return to_route('agreements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Agreement $agreement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agreement $agreement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Agreement $agreement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agreement $agreement)
    {
        //
    }
}
