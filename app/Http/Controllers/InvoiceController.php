<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\CustomerCategory;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceComment;
use App\Models\InvoiceProduct;
use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;  // Import Carbon for date manipulation

class InvoiceController extends Controller
{
    // Show the invoice creation page with dropdown options for agreements, quotations, and customers
    public function create()
    {
        // Get all agreements, quotations, customers, and products
        $agreements = Agreement::all();
        $quotations = Quotation::with(["productQuotations","agreement","productQuotations.product"])->where("status","Approved")->get();
        $customers = Customer::all();
        $products = Product::all();

        // Pass the full models to the form
        return Inertia::render('Invoices/Create', [
            'agreements' => $agreements,
            'quotations' => $quotations,
            'customers' => $customers,
            'products' => $products,
        ]);
    }
    public function show()
    {
        // Get all agreements, quotations, customers, and products
        $agreements = Agreement::all();
        $quotations = Quotation::with(["productQuotations","agreement","productQuotations.product"])->where("status","Approved")->get();
        $customers = Customer::all();
        $products = Product::all();

        // Pass the full models to the form
        return Inertia::render('Invoices/Show', [
            'agreements' => $agreements,
            'quotations' => $quotations,
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    // Store a newly created invoice
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'invoice_no' => 'nullable|unique:invoices,invoice_no',
            'status' => 'required|string|in:Pending,Approved,Revise',
            'agreement_no' => 'nullable|integer|exists:agreements,agreement_no',
            'quotation_no' => 'nullable|integer|exists:quotations,quotation_no',
            'customer_id' => 'required|exists:customers,id',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'terms' => 'nullable|string|max:255',
            'total_usd' => 'nullable|numeric',
            'exchange_rate' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|string', 
            'end_date' => 'nullable|string',   
            'products' => 'nullable|array',
            'products.*.id' => 'required|integer',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.acc_code' => 'nullable|string',
            'products.*.category_id' => 'nullable|integer',
            'products.*.remark' => 'nullable|string',
            'products.*.include_catalog' => 'required|boolean',
            'products.*.pdf_url' => 'nullable|string',
        ]);
    
        if ($validated->fails()) {
            return response()->json(['message' => $validated->errors()], 422);
        }
    
        $data = $validated->validated();
    
        // Format dates (convert from dd/mm/yyyy to Y-m-d format for storing in DB)
        $startDate = now()->format('Y-m-d');
        if (!empty($data['start_date']) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['start_date'])) {
            $startDate = Carbon::createFromFormat('d/m/Y', $data['start_date'])->format('Y-m-d');
        }
    
        $endDate = now()->format('Y-m-d');
        if (!empty($data['end_date']) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['end_date'])) {
            $endDate = Carbon::createFromFormat('d/m/Y', $data['end_date'])->format('Y-m-d');
        }
    
        // Calculate grand total
        $grand_total = 0;
        if (!empty($data['products'])) {
            foreach ($data['products'] as $product) {
                $productData = Product::find($product['id']);
                if ($productData && $productData->status === 'approved') {
                    $grand_total += $product['price'] * $product['quantity'];
                }
            }
        }
    
        // Optional USD conversion
        $total_usd = isset($data['exchange_rate']) && $data['exchange_rate'] > 0
            ? $grand_total / $data['exchange_rate']
            : $data['total_usd'] ?? null;
    
        // Auto-generate invoice number only if status is Approved
        $invoice_no = null;
        if ($data['status'] === 'Approved') {
            $currentYear = date('Y');
            $baseInvoiceNo = ($currentYear - 2025) * 1000000 + 25000001;
            $lastInvoice = \App\Models\Invoice::where('invoice_no', '>=', $baseInvoiceNo)
                            ->orderBy('invoice_no', 'desc')
                            ->first();
            $invoice_no = $lastInvoice ? $lastInvoice->invoice_no + 1 : $baseInvoiceNo;
        }
    
        // Auto-generate invoice_date only if status is Approved
        $invoiceDate = null;
    
        if ($data['status'] === 'Approved') {
            $invoiceDate = now()->format('Y-m-d H:i:s');
        } elseif (
            $data['status'] !== 'Pending' &&
            !empty($request->invoice_date) &&
            strtotime($request->invoice_date)
        ) {
            $invoiceDate = Carbon::parse($request->invoice_date)->format('Y-m-d H:i:s');
        }    
    
        // Create invoice
        $invoice = \App\Models\Invoice::create([
            'invoice_no'     => $invoice_no,
            'quotation_no'   => $data['quotation_no'] ?? null,
            'agreement_no'   => $data['agreement_no'] ?? null,
            'customer_id'    => $data['customer_id'],
            'address'        => $data['address'] ?? null,
            'phone'          => $data['phone'] ?? null,
            'terms'          => $data['terms'] ?? null,
            'start_date'     => $startDate,
            'end_date'       => $endDate,
            'grand_total'    => $grand_total,
            'total_usd'      => $total_usd,
            'exchange_rate'  => $data['exchange_rate'] ?? null,
            'invoice_date'   => $invoiceDate,
            'status'         => $data['status'],
        ]);
    
        // Attach multiple products to the pivot table (invoice_product)
        if (!empty($data['products'])) {
            foreach ($data['products'] as $product) {
                $productData = Product::find($product['id']);
                if ($productData && $productData->status === 'approved') {
                    $invoice->products()->attach($product['id'], [
                        'quantity' => $product['quantity'],
                        'price' => $product['price'],
                        'include_catalog' => $product['include_catalog'],
                        'pdf_url' => $product['pdf_url'] ?? null,
                    ]);
                }
            }
        }
    
        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully!');
    }

    public function updateStatus(Request $request, Invoice $invoice)
{   

    $validated = $request->validate([
        'status' => 'required|string|in:approved,revise,pending',
        'comment' => 'nullable|string|max:1000',
        'role' => 'nullable|string', // optional
    ]);

    $invoice->status = $validated['status'];

    if ($invoice->status === 'approved') {
        if (!$invoice->invoice_no) {
            $year = date('Y');
            $baseInvoiceNo = ($year - 2025) * 1000000 + 25000001;
            $last = Invoice::where('invoice_no', '>=', $baseInvoiceNo)
                ->orderBy('invoice_no', 'desc')
                ->first();
            $invoice->invoice_no = $last ? $last->invoice_no + 1 : $baseInvoiceNo;
        }

        $invoice->invoice_date = now();
    }

    $invoice->save();

    if (!empty($validated['comment'])) {
        \App\Models\InvoiceComment::create([
            'invoice_id' => $invoice->id,
            'status' => $validated['status'],
            'comment' => $validated['comment'],
        ]);
    }

    return;
}


    // Show the list of invoices
    public function index(Request $request)
{
    // Initialize the query builder for the invoices
    $query = Invoice::with('customer', 'agreement', 'quotation', 'products', 'customer_category', 'invoiceComments');

    // Apply the filters from the request, if any

    // Filter by invoice number (if provided)
    if ($request->has('invoice_no_start') && $request->invoice_no_start) {
        $query->where('invoice_no', '>=', $request->invoice_no_start);
    }

    if ($request->has('invoice_no_end') && $request->invoice_no_end) {
        $query->where('invoice_no', '<=', $request->invoice_no_end);
    }

    // Filter by customer name (if provided)
    if ($request->has('customer') && $request->customer) {
        $query->whereHas('customer', function($query) use ($request) {
            $query->where('name', 'like', '%' . $request->customer . '%');
        });
    }

    // Filter by status (if provided)
    if ($request->has('status') && $request->status) {
        $query->where('status', 'like', '%' . $request->status . '%');
    }

    // Filter by start date
    if ($request->has('start_date') && $request->start_date) {
        $query->where('start_date', '>=', $request->start_date);
    }

    // Filter by end date
    if ($request->has('end_date') && $request->end_date) {
        $query->where('end_date', '<=', $request->end_date);
    }

    // Filter by customer category (if provided)
    if ($request->has('category_name_english') && $request->category_name_english) {
        $query->whereHas('customer.customerCategory', function ($query) use ($request) {
            $query->where('category_name_english', 'like', '%' . $request->category_name_english . '%');
        });
    }

    // Filter by currency
    if ($request->has('currency') && $request->currency) {
        $query->where('currency', $request->currency);
    }

    // Apply pagination after all filters
    $invoices = $query->paginate();  // Apply pagination after the filters are added

    // Get the filters for passing them to the view
    $filters = $request->only(['invoice_no_start', 'invoice_no_end', 'category_name_english', 'currency', 'status', 'start_date', 'end_date', 'customer']);

    // Return the filtered invoices to the view
    return Inertia::render('Invoices/Index', [
        'invoices' => $invoices,
        'filters' => $filters,
    ]);
}

public function list(Request $request)
{
    // Initialize the query builder for the invoices
    $query = Invoice::with('customer', 'agreement', 'quotation', 'products', 'customer_category', 'invoiceComments');

    // Apply the filters from the request, if any

    // Filter by invoice number (if provided)
    if ($request->has('invoice_no_start') && $request->invoice_no_start) {
        $query->where('invoice_no', '>=', $request->invoice_no_start);
    }

    if ($request->has('invoice_no_end') && $request->invoice_no_end) {
        $query->where('invoice_no', '<=', $request->invoice_no_end);
    }

    // Filter by customer name (if provided)
    if ($request->has('customer') && $request->customer) {
        $query->whereHas('customer', function($query) use ($request) {
            $query->where('name', 'like', '%' . $request->customer . '%');
        });
    }

    // Filter by status (if provided)
    if ($request->has('status') && $request->status) {
        $query->where('status', 'like', '%' . $request->status . '%');
    }

    // Filter by start date
    if ($request->has('start_date') && $request->start_date) {
        $query->where('start_date', '>=', $request->start_date);
    }

    // Filter by end date
    if ($request->has('end_date') && $request->end_date) {
        $query->where('end_date', '<=', $request->end_date);
    }

    // Filter by customer category (if provided)
    if ($request->has('category_name_english') && $request->category_name_english) {
        $query->whereHas('customer.customerCategory', function ($query) use ($request) {
            $query->where('category_name_english', 'like', '%' . $request->category_name_english . '%');
        });
    }

    // Filter by currency
    if ($request->has('currency') && $request->currency) {
        $query->where('currency', $request->currency);
    }

    // Apply pagination after all filters
    $invoices = $query->paginate();  // Apply pagination after the filters are added

    // Get the filters for passing them to the view
    $filters = $request->only(['invoice_no_start', 'invoice_no_end', 'category_name_english', 'currency', 'status', 'start_date', 'end_date', 'customer']);

    // Return the filtered invoices to the view
    return Inertia::render('Invoices/Index', [
        'invoices' => $invoices,
        'filters' => $filters,
    ]);
}

    // Show the edit page for an existing invoice
    public function edit($id)
    {
        $invoice = Invoice::with('customer', 'products')->findOrFail($id);

        $agreements = Agreement::all();
        $quotations = Quotation::all();
        $customers = Customer::all(); // Pass the full Customer model instances
        $products = Product::all(); // Pass the full Product model instances

        return Inertia::render('Invoices/Edit', [
            'invoice' => $invoice,
            'agreements' => $agreements,
            'quotations' => $quotations,
            'customers' => $customers, // Pass the full models to the form
            'products' => $products, // Pass the full Product models
        ]);
    }

    // Update an existing invoice
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'invoice_no' => 'required|unique:invoices,invoice_no,' . $id,
            'agreement_no' => 'required',
            'quotation_no' => 'required',
            'customer_id' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'products' => 'nullable|array', // Make products optional
            'products.*.id' => 'required|exists:products,id', // Validate product IDs
            'products.*.quantity' => 'required|numeric|min:1', // Validate product quantities
        ]);

        $invoice = Invoice::findOrFail($id);

        // Format start_date and end_date using Carbon
        $startDate = Carbon::parse($validated['start_date'])->toDateTimeString();
        $endDate = Carbon::parse($validated['end_date'])->toDateTimeString();

        // Calculate the new grand total based on the selected products
        $grand_total = 0;
        foreach ($validated['products'] as $product) {
            $prod = Product::find($product['id']);
            $grand_total += $prod->price * $product['quantity'];
        }

        // Assuming tax is still 10%
        $tax = 0.00 * $grand_total;
        $grand_total += $tax;  // Add tax to grand total

        // Update the invoice with the validated data
        $invoice->update([
            'invoice_no' => $validated['invoice_no'],
            'agreement_no' => $validated['agreement_no'],
            'quotation_no' => $validated['quotation_no'],
            'customer_id' => $validated['customer_id'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'grand_total' => $grand_total,
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1', // Store only the grand total
        ]);

        // Handle updating the products associated with this invoice
        if (isset($validated['products'])) {
            // First, detach all current products
            $invoice->products()->detach();

            // Attach the new products with their quantities
            foreach ($validated['products'] as $product) {
                $invoice->products()->attach($product['id'], ['quantity' => $product['quantity']]);
            }
        }

        return redirect()->route('invoices.index')->with('success', 'Invoice updated successfully!');
    }

    // Delete an invoice
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Detach any products related to this invoice before deletion
        $invoice->products()->detach();

        // Delete the invoice
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully!');
    }

    // public function show($id)
    // {
    //     $invoice = Invoice::with(['customer', 'products'])->findOrFail($id);

    //     return Inertia::render('Invoices/Show', [
    //         'invoice' => $invoice
    //     ]);
    // }

    public function getInvoicesByQuotation($quotation_no)
    {
        // Fetch invoices for the given quotation_no and status=paid
        $invoices = Invoice::where('quotation_no', $quotation_no)
                            ->where('status', 'paid')
                            ->get();

        // Return the invoices as a JSON response
        return response()->json($invoices);
    }
}
