<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // Import Validator facade
use Carbon\Carbon; // Import Carbon for date manipulation
use App\Models\Quotation; // Import Quotation model
use App\Models\Product; // Import Product model
use App\Models\Customer; // Import Customer model
use App\Models\Agreement; // Import Customer model
use App\Models\CustomerCategory; // Import Customer model
use App\Models\ProductQuotation;
use App\Models\Division;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{
    public function list()
    {
        $quotations = Quotation::with(['customer', 'products','comments','latestComment'])->orderBy('created_at', 'desc')->get();
        $agreements = Agreement::all();
        $customers = Customer::all();
        $products = Product::all();

        Log::info($quotations);
        return Inertia::render('Quotations/List', [ // Render the index page using Inertia
            'quotations' => $quotations,
            'agreements' => $agreements,
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    public function create(Request $request)
{
    $products = Product::where('status', 'approved')->get();
    $quotation = $request->input('quotation', null);

    $customers = Customer::all();
    $divisions = Division::all();
    $products = Product::all();
    $customerCategories = CustomerCategory::all();
    $productCategories = Category::all();
    return inertia('Quotations/Create', [
        'customers' => Customer::select('id', 'name', 'address', 'phone_number')->get(),
        // 'products' => $products,
        'products' => Product::where('status', 'approved')->get(),
        'customerCategories' => CustomerCategory::all(),
        'productCategories' => Category::all(),
        'quotation' => $quotation,
        'divisions' => $divisions,
    ]);
}

    public function updateStatus(Request $request, $id)
{
    $quotation = Quotation::findOrFail($id);

    $newStatus         = $request->input('status');
    $newCustomerStatus = $request->input('customer_status');
    $comment           = $request->input('comment');
    $userRole          = $request->input('role');
    // If you're using authentication with roles, you could do: $userRole = $request->user()->role

    // 1. If status is Approved or Revised, and there's no quotation_no yet, assign one
    if (($newStatus === 'Approved' || $newStatus === 'Revised') && !$quotation->quotation_no) {
        $lastQuotation = Quotation::orderBy('quotation_no', 'desc')->first();
        $quotation->quotation_no = $lastQuotation
            ? $lastQuotation->quotation_no + 1
            : 25000001;
    }

    // 2. If status is Approved or Revised, and there's no quotation_date yet, set it
    if (($newStatus === 'Approved' || $newStatus === 'Revised') && !$quotation->quotation_date) {
        $quotation->quotation_date = now();
    }

    // 3. If status is Revised, set the revised_at timestamp
    if ($newStatus === 'Revised') {
        $quotation->revised_at = now();
    }

    // 4. Update the Quotation status and customer status
    $quotation->status = $newStatus;
    if ($newCustomerStatus) {
        $quotation->customer_status = $newCustomerStatus;
    }

    // 5. Save the changes to the quotation
    $quotation->save();

    // 6. If a comment is provided, store it in the quotation_comments table
    if (!empty($comment)) {
        $quotation->comments()->create([
            'user_id' => $request->user()->id ?? null,
            'role'    => $userRole,
            'status'  => $newStatus,
            'comment' => $comment,
        ]);
    }
}


    public function storeComment(Request $request, $quotationId)
{
    $quotation = Quotation::findOrFail($quotationId);

    $comment = $request->input('comment');
    $role    = $request->input('role'); // 'ITC management', 'ITC customer', etc.

    $quotation->comments()->create([
        'user_id' => $request->user()->id ?? null,
        'role'    => $role,
        'status'  => $quotation->status, // store the Quotation's current status
        'comment' => $comment,
    ]);

    return response()->json([
        'message'  => 'Comment added successfully!',
        'comments' => $quotation->comments()->latest()->get(),
    ]);
}

    public function store(Request $request)
    {

        // Validate the incoming request
        $validated = Validator::make($request->all(), [
            'quotation_no'   => 'nullable|integer|unique:quotations,quotation_no',
            'quotation_date' => 'nullable|date_format:Y-m-d\TH:i:s.v\Z',
            'customer_id'    => 'required|exists:customers,id',
            'address'        => 'nullable|string|max:255',
            'phone_number'   => 'nullable|string|max:20',
            'terms'          => 'nullable|string|max:255',
            // 'tax'            => 'required|numeric',
            'products'       => 'nullable|array', // Make products optional
            'products.*.id' => 'required|exists:products,id', // Validate product IDs
            'products.*.quantity' => 'required|numeric|min:1', // Validate product quantities
            'products.*.price' => 'required|numeric|min:1', // Validate product quantities
            'products.*.acc_code' => 'nullable|string|max:255',
            'products.*.category_id' => 'nullable|integer|min:0',
            'products.*.remark' => 'nullable|string|max:255',
            'products.*.pdf' => 'nullable|file|mimes:pdf|max:2048', // ✅ Make it optional
            'products.*.include_catalog' => 'required|boolean', // Ensure include_catalog is validated
            'products.*.pdf_url' => 'nullable|string|max:255',
        ]);
        if ($validated->fails()) {
            return response()->json(['message' => $validated->errors()], 422);
        }

        // Get validated data
        $validated = $validated->validated();

        // Calculate the total
        $total = 0;
        if (isset($validated['products'])) {
            foreach ($validated['products'] as $product) {
                // Only add approved products to the total
                $productData = Product::find($product['id']);
                if ($productData && $productData->status === 'approved') {
                    $total += $product['price'] * $product['quantity'];
                }
            }
        }
        // Calculate tax based on the provided percentage
        // $tax = $validated['tax'] / 100;
        // $tax = $tax * $total;
        // $grand_total = $total + $tax;
        // $grand_total = $total;

        $quotationDate = $request->quotation_date
        ? Carbon::parse($request->quotation_date)->format('Y-m-d H:i:s')
        : now()->format('Y-m-d H:i:s');

        // Get the last used quotation_no and increment it
        // $lastQuotation = Quotation::orderBy('quotation_no', 'desc')->first();
        // $newQuotationNo = $lastQuotation ? $lastQuotation->quotation_no + 1 : 25000001;

        $currentYear = date('Y');
        $baseQuotationNo = ($currentYear - 2025) * 1000000 + 25000001;
        $lastQuotation = Quotation::where('quotation_no', '>=', $baseQuotationNo)
                                ->orderBy('quotation_no', 'desc')
                                ->first();
        $newQuotationNo = $lastQuotation ? $lastQuotation->quotation_no + 1 : $baseQuotationNo;

        // dd(json_encode($validated["products"], true));
        // Create the quotation
        $quotation = Quotation::create([
            // 'quotation_no'   => $newQuotationNo,  // Use the newly generated quotation_no
            'customer_id'    => $validated['customer_id'],
            'address'        => $validated['address'] ?? null,
            'phone_number'   => $validated['phone_number'] ?? null,
            'terms'          => $validated['terms'] ?? null,
            'total'          => $total,

        ]);
        // Attach products to the quotation
        if (isset($validated['products'])) {
            foreach ($validated['products'] as $product) {
                $productData = Product::find($product['id']);

                // Only attach approved products
                if ($productData && $productData->status === 'approved') {
                    // Debug the product data
                    \Log::info('Product Data:', $product);
                    ProductQuotation::create([
                        'product_id' => $product['id'],
                        'quantity'   => $product['quantity'],
                        'quotation_no' => $quotation->id,
                        'price'      => $product['price'],
                        'include_catalog' => $product['include_catalog'],
                        'pdf_url'    => $product['pdf_url'] ?? null,
                    ]);
                }
                // $quotation->products()->attach($product['id'], [
                //     'quantity' => $product['quantity'],
                //     'price'    => $product['price'],
                //     'product_unit_prices' => json_encode($validated['products']),
                // ]);
            }
        }
    // Redirect with a success message
    return redirect()->route('quotations.list')->with('success', 'Quotation created successfully!');
}

    /**
     * For printing quotations.
     */
    public function show($quotation_no)
    {
        // $quotation = Quotation::with(['customer', 'products'])->findOrFail($quotation_no);
        $quotation = Quotation::with(['customer', 'products'])
        ->where('id', $quotation_no)
        ->firstOrFail();
        Log::info("message");
        // return Inertia::render('Quotations/Print', [
            //     'quotation' => $quotation,
            //     'products' => $quotation->products,
            // ]);
        $quotation = Quotation::with(['customer', 'products' => function($query) {
            $query->withPivot(['quantity', 'price', 'include_catalog']);
        }])->where('id', $quotation_no)->firstOrFail();
        $formattedQuotationDate = $quotation->quotation_date
        ? Carbon::parse($quotation->quotation_date)->format('Y-m-d')
        : null;
        return Inertia::render('Quotations/Print', [
            'quotation' => [
                'id' => $quotation->id,
                'quotation_no' => $quotation->quotation_no ?? 'Pending',
                'quotation_date' => $quotation->quotation_date ? Carbon::parse($quotation->quotation_date)->format('Y-m-d') : null,
                'customer_id' => $quotation->customer_id,
                'customer_name' => $quotation->customer->name,
                'address' => $quotation->address,
                'phone_number' => $quotation->phone_number,
                'products' => $quotation->products, // ✅ Ensures products with pivot data are passed
                'total' => $quotation->total,
                'terms' => $quotation->terms,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quotation $quotation)
    {
        $products = $quotation->products;
        // Render a form for editing an existing quotation
        return Inertia::render('Quotations/Edit', [
            'quotation' => $quotation,
            'products' => $products,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quotation $quotation)
    {
        // Validate and update the quotation data
        $validated = $request->validate([
            // 'quotation_no' => 'sometimes|integer',
            // 'quotation_date' => 'sometimes|date',
            'customer_id' => 'required|exists:customers,id',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'terms' => 'nullable|string|max:255',
            'total' => 'required|numeric|min:0',
            // 'tax' => 'required|numeric|min:0',
            // 'grand_total' => 'required|numeric|min:0',
            // 'status' => 'sometimes|string|max:20',
            'products'        => 'nullable|array',
            'products.*.id'   => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.price'=> 'required|numeric|min:1',
            'products.*.acc_code' => 'nullable|string|max:255',
            'products.*.category_id' => 'nullable|integer|min:0',
            'products.*.remark' => 'nullable|string|max:255',
            // 'products.*.pdf' => 'nullable|file|mimes:pdf|max:2048', // if needed
            'products.*.include_catalog' => 'required|boolean',
            'products.*.pdf_url' => 'nullable|string|max:255',

        ]);

        $quotation->update($validated);

        // Update associated products
        foreach ($validated['products'] as $product) {
            $existingProduct = ProductQuotation::where([
                'product_id' => $product['id'],
                'quotation_no' => $quotation->id,
            ])->first();

            if ($existingProduct) {
                $existingProduct->update([
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'include_catalog' => $product['include_catalog'],
                    'pdf_url' => $product['pdf_url'] ?? null,
                ]);
            }
        }

        return redirect()->route('quotations.list')->with('success', 'Quotation updated successfully.');
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
    //             ];
    //         }),
    //     ]);
    // }
}
