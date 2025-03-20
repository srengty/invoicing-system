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
use Illuminate\Support\Facades\Mail;
use App\Mail\QuotationEmail;
use Illuminate\Support\Facades\Http;
use Exception;

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

    public function savePDF(Request $request)
{
    // Validate the uploaded file (optional)
    $request->validate([
        'pdf_file' => 'required|file|mimes:pdf|max:2048',
    ]);

    // Generate a custom filename based on quotation ID or timestamp
    $quotation = Quotation::find($request->input('quotation_id'));
    $fileName = 'quotation_' . $quotation->id . '.pdf';  // Use the quotation ID or any other unique identifier

    // Store the PDF in the public/quotations folder with a custom filename
    $pdfPath = $request->file('pdf_file')->storeAs('public/quotations', $fileName);

    // Log the saved file path for debugging
    Log::info('PDF saved to: ' . $pdfPath);

    // If you want to get the absolute path
    $fullPath = storage_path('app/' . $pdfPath);

    return response()->json([
        'success' => true,
        'message' => 'PDF saved successfully!',
        'path' => $pdfPath, // Return the path if needed for later use
        'full_path' => $fullPath // Include the absolute path for the file
    ]);
}


public function sendQuotation(Request $request)
{
    // Find the quotation by ID
    $quotation = Quotation::with('customer')->find($request->input('quotation_id'));

    if (!$quotation) {
        return response()->json(['error' => 'Quotation not found'], 404);
    }

    try {
        // Ensure the customer has a valid email
        $customerEmail = $quotation->customer->email;

        if (!$customerEmail) {
            throw new Exception('Customer email not found.');
        }

        // Save the PDF file to storage
        if ($request->hasFile('pdf_file')) {
            $pdfFile = $request->file('pdf_file');
            $pdfPath = $pdfFile->store('quotations', 'public'); // Save to 'storage/app/public/quotations'
            Log::info('PDF saved to: ' . $pdfPath); // Log the saved path
        } else {
            throw new Exception('PDF file not found.');
        }

        // Send email if requested
        if ($request->input('send_email')) {
            // Log email sending attempt
            Log::info('Sending email to: ' . $customerEmail);

            // Ensure the file path for email is correct and accessible
            $filePath = storage_path('app/public/' . $pdfPath);

            // Send the email with the saved PDF file
            Mail::to($customerEmail)->send(new QuotationEmail($quotation, $filePath));

            // Check if there were any failures when sending the email
            if (Mail::failures()) {
                throw new Exception('Failed to send email to ' . $customerEmail);
            }
        }

        // Return success response
        return response()->json([
            'success' => 'Quotation sent successfully via email',
            'pdf_path' => $pdfPath, // Optional: Return the saved PDF path
        ]);
    } catch (Exception $e) {
        // Log any error and return a failure response
        Log::error('Failed to send quotation: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}
