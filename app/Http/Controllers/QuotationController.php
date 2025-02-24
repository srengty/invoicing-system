<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // Import Validator facade
use Carbon\Carbon; // Import Carbon for date manipulation
use App\Models\Quotation; // Import Quotation model
use App\Models\Product; // Import Product model
use App\Models\Customer; // Import Customer model
use App\Models\Agreement; // Import Customer model
use App\Models\ProductQuotation;
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
        $products = Product::all();
//         dd($products);
        return inertia('Quotations/Create', [
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    public function updateStatus(Request $request, $id) {
        $quotation = Quotation::findOrFail($id);
        $quotation->status = $request->status;  // Ensure correct data is saved
        $quotation->save();

        return response()->json(['message' => 'Quotation status updated successfully!']);
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
            // 'tax'            => 'required|numeric',
            'products'       => 'nullable|array', // Make products optional
            'products.*.id' => 'required|exists:products,id', // Validate product IDs
            'products.*.quantity' => 'required|numeric|min:1', // Validate product quantities
              'products.*.price' => 'required|numeric|min:1', // Validate product quantities
        ]);
        if ($validated->fails()) {
            return response()->json(['message' => $validated->errors()], 422);
        }

        // Get validated data
        $validated = $validated->validated();

        // Calculate the total
        $total = 0;
        foreach ($validated['products'] as $product) {

//             $prod = Product::find($product['id']);
            $total += $product['price'] * $product['quantity'];
        }
        // Calculate tax based on the provided percentage
        // $tax = $validated['tax'] / 100;
        // $tax = $tax * $total;
        // $grand_total = $total + $tax;
        // $grand_total = $total;

        $quotationDate = Carbon::parse($request->quotation_date)->format('Y-m-d H:i:s');

        // Get the last used quotation_no and increment it
        $lastQuotation = Quotation::orderBy('quotation_no', 'desc')->first();
        $newQuotationNo = $lastQuotation ? $lastQuotation->quotation_no + 1 : 25000001;  // Start from a default value if none exists

// dd(json_encode($validated["products"], true));
        // Create the quotation
        $quotation = Quotation::create([
            'quotation_no'   => $newQuotationNo,  // Use the newly generated quotation_no
            'quotation_date' => $quotationDate,
            'customer_id'    => $validated['customer_id'],
            'address'        => $validated['address'] ?? null,
            'phone_number'   => $validated['phone_number'] ?? null,
            'terms'          => $validated['terms'] ?? null,
            'total'          => $total,

        ]);

        $quotation->save();

        // Attach products to the quotation
        foreach ($validated['products'] as $product) {
            $prod = Product::find($product['id']);
            $quotation->products()->attach($prod->id, [
                'quantity' => $product['quantity'],
//                 'price' => $prod->price * $product['quantity'],
                'price' => $prod->price,
                'product_unit_prices' => json_encode($validated["products"], true),
            ]);
        }

    // Redirect with a success message
    return redirect()->route('quotations.list')->with('success', 'Quotation created successfully!');
}

    /**
     * For printing quotations.
     */
    public function show($quotation_no)
    {
        $quotation = Quotation::with(['customer', 'products'])->findOrFail($quotation_no);

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
            // 'tax' => 'required|numeric|min:0',
            // 'grand_total' => 'required|numeric|min:0',
            'status' => 'required|string|max:20',
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

    public function getAgreementForQuotation($quotationId)
    {
        // Fetch the quotation using the provided ID
        $quotation = Quotation::find($quotationId);

        // If the quotation is not found, return an error message
        if (!$quotation) {
            return response()->json(['message' => 'Quotation not found'], 404);
        }

        // If the quotation exists, fetch the related agreement
        $agreement = $quotation->agreement; // Assuming `agreement` is a relation on the Quotation model

        // If the agreement is not found, return an error message
        if (!$agreement) {
            return response()->json(['message' => 'No agreement found for this quotation'], 404);
        }

        // Return the agreement as JSON response
        return response()->json($agreement);
    }

    // public function show($id)
    // {
    //     $quotation = Quotation::with(['customer', 'items', 'agreement'])->findOrFail($id);

    //     return response()->json([
    //         'customer_name' => $quotation->customer->name,
    //         'customer_address' => $quotation->customer->address,
    //         'customer_phone' => $quotation->customer->phone,
    //         'agreement' => $quotation->agreement ? ['agreement_no' => $quotation->agreement->number] : null,
    //         'items' => $quotation->items->map(function ($item) {
    //             return [
    //                 'id' => $item->id,
    //                 'name' => $item->name,
    //                 'unit_price' => $item->unit_price,
    //                 'quantity' => $item->quantity,
    //             ];
    //         }),
    //     ]);
    // }

}


