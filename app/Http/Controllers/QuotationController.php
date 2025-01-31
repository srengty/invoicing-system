<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        // Fetch all quotations with their associated customers
        $quotations = Quotation::with('customer')->get();

        // Render the index page using Inertia
        return Inertia::render('Quotations/List', [
            'quotations' => $quotations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Render a form for creating a new quotation
        // return Inertia::render('Quotations/Create');
        $customers = Customer::select('name', 'id')->get(); // Fetch customer id and name`
        $products = Product::select('name', 'unit', 'price','quantity', 'id')->get(); // Fetch customer id and name
        return inertia('Quotations/Create', [
            'customers' => $customers, // Pass customers to the frontend
            'products' => $products, // Pass customers to the frontend
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'quotation_no' => 'required|integer|unique:quotations,quotation_no',
            'quotation_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'terms' => 'nullable|string|max:255',
        ]);
    
        // Create a new quotation record
        $quotation = Quotation::create($validated);
    
        // Update the quotation with additional fields
        $quotation->terms = $request->input('terms') ?? 'default terms'; // Default value for terms
        $quotation->total = $request->input('total') ?? 0.00; // Default value for total
        $quotation->tax = $request->input('tax') ?? 0.00; // Default value for tax
        $quotation->grand_total = $request->input('grand_total') ?? 0.00; // Default value for grand_total
        $quotation->status = $request->input('status') ?? 'pending'; // Default value for status
        $quotation->customer_status = $request->input('customer_status') ?? 'active'; // Default value for customer_status
    
        // Save the updated quotation
        $quotation->save();
    
        // Redirect to the quotations list with a success message
        return redirect()->route('quotations.list')->with('success', 'Quotation created successfully.');
    }
    /**
     * Display the specified resource.
     */
    public function show(Quotation $quotation)
    {
        // Show details of a single quotation
        return Inertia::render('Quotations/Show', [
            'quotation' => $quotation->load('customer'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quotation $quotation)
    {
        // Render a form for editing an existing quotation
        return Inertia::render('Quotations/Edit', [
            'quotation' => $quotation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quotation $quotation)
    {
        // Validate and update the quotation data
        $validated = $request->validate([
            'quotation_no' => 'required|integer|unique:quotations,quotation_no,' . $quotation->id,
            'quotation_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'terms' => 'nullable|string|max:255',
            'total' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'status' => 'required|string|max:20',
            'customer_status' => 'required|string|max:20',
        ]);

        $quotation->update($validated);

        return redirect()->route('quotations.index')->with('success', 'Quotation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quotation $quotation)
    {
        // Delete the quotation
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully.');
    }
}
