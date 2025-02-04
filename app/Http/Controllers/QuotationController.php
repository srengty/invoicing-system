<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // Import Validator facade
use Carbon\Carbon; // Import Carbon for date manipulation
use App\Models\Quotation; // Import Quotation model
use App\Models\Product; // Import Product model
use App\Models\Customer; // Import Customer model
use App\Models\Agreement; // Import Customer model
use Inertia\Inertia;
class QuotationController extends Controller
{
    public function list()
    {
        $quotations = Quotation::with('customer', 'products')->get();
        $agreements = Agreement::all();
        $customers = Customer::all();
        $products = Product::all();
        
        return Inertia::render('Quotations/List', [ // Render the index page using Inertia
            'quotations' => $quotations,
            'agreements' => $agreements,
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    public function create()
    {
        $customers = Customer::all(); // Fetch customer id and name`
        $products = Product::select('name', 'unit', 'price', 'id')->get(); // Fetch customer id and name
        return inertia('Quotations/Create', [
            'customers' => $customers, 
            'products' => $products,   
        ]);
    }

    public function store(Request $request)
    {
        
        // Validate the incoming request
        $validated = Validator::make($request->all(), [
            'quotation_no'   => 'required|integer|unique:quotations,quotation_no',
            'quotation_date' => 'required|date_format:Y-m-d\TH:i:s.v\Z',
            'customer_id'    => 'required|exists:customers,id',
            'address'        => 'nullable|string|max:255',
            'phone_number'   => 'nullable|string|max:20',
            'terms'          => 'nullable|string|max:255',
            'tax'            => 'required|numeric',
            'products'       => 'nullable|array', // Make products optional
            'products.*.id' => 'required|exists:products,id', // Validate product IDs
            'products.*.quantity' => 'required|numeric|min:1', // Validate product quantities
        ]);
    
        if ($validated->fails()) {
            return response()->json(['message' => $validated->errors()], 422);
        }

        // Get validated data
        $validated = $validated->validated();

        // Calculate the total, tax, and grand total
        $total = 0;
        foreach ($validated['products'] as $product) {
            $prod = Product::find($product['id']);
            $total += $prod->price * $product['quantity'];
        }
        // Calculate tax based on the provided percentage
        $tax = $validated['tax'] / 100; 
        $tax = $tax * $total;
        $grand_total = $total + $tax;

        // Format the datetime value
        $quotationDate = Carbon::parse($request->quotation_date)->format('Y-m-d H:i:s');
        // Create the quotation
        $quotation = Quotation::create([
            'quotation_no'   => $validated['quotation_no'],
            'quotation_date' => $quotationDate,
            'customer_id'    => $validated['customer_id'],
            'address'        => $validated['address'] ?? null,
            'phone_number'   => $validated['phone_number'] ?? null,
            'terms'          => $validated['terms'] ?? null,
            'total'          => $total,
            'tax'            => $tax,
            'grand_total'    => $grand_total,
        ]);

        // Set default values for status and customer_status
        $quotation->status = $request->input('status') ?? 'pending'; // Default value for status
        $quotation->customer_status = $request->input('customer_status') ?? 'active'; // Default value for customer_status
        $quotation->save();

        // Attach the selected products with their quantities to the quotation
        foreach ($validated['products'] as $product) {
            $quotation->products()->attach($product['id'], ['quantity' => $product['quantity']]);
        }

        // Redirect with a success message
        return redirect()->route('quotations.list')->with('success', 'Quotation created successfully!');
    }
    
    /**
     * Display for print quotations.
     */
    public function show($id)
    {
        $quotation = Quotation::with(['customer', 'products'])->findOrFail($id);

        return Inertia::render('Quotations/Print', [
            'quotation' => $quotation,
            'products' => $quotation->products,
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

        return redirect()->route('quotations.list')->with('success', 'Quotation deleted successfully.');
    }
}
