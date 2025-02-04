<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Customer;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'csrf_token' => csrf_token(),
            'quotations' => Quotation::all(),
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
    public function show(int $id)
    {
        return Inertia::render('Agreements/Show', [
            'agreement' => Agreement::with('customer')->find($id),
        ]);
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
     * Upload the specified resource into storage.
     */
    public function upload(Request $request)
    {
        if($request->has('agreement_doc_old')){
            Storage::disk('public')->delete('agreements/'.basename($request->agreement_doc_old));
        }
        return Storage::url($request->file('agreement_doc')->storePublicly('agreements','public'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agreement $agreement)
    {
        //
    }
}
