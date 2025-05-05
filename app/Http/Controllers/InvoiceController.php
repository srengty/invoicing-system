<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\CustomerCategory;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceComment;
use App\Models\PaymentSchedule;
use App\Models\Receipt;
use App\Models\InvoiceProduct;
use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;  // Import Carbon for date manipulation
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceEmail;
use Illuminate\Support\Facades\DB;

use Exception;

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
        $paymentSchedules = PaymentSchedule::all();
        $receipts = Receipt::all();

        // Pass the full models to the form
        return Inertia::render('Invoices/Create', [
            'agreements' => $agreements,
            'quotations' => $quotations,
            'customers' => $customers,
            'products' => $products,
            'paymentSchedules' => $paymentSchedules,
            'receipts'=> $receipts,
        ]);
    }

    // Store a newly created invoice
    public function store(Request $request)
    {
        // Validate the request data
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
            'payment_schedule_id' => 'nullable|integer|exists:payment_schedules,id',
            'currency' => 'nullable|string|in:KHR,USD',
            'start_date' => 'nullable|string', 
            'end_date' => 'nullable|string',
            'paid_amount' => 'nullable|numeric|min:0',  
            'products' => 'nullable|array',
            'products.*.id' => 'required|integer',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.acc_code' => 'nullable|string',
            'products.*.category_id' => 'nullable|integer',
            'products.*.remark' => 'nullable|string',
            'products.*.include_catalog' => 'required|boolean',
            'products.*.pdf_url' => 'nullable|string',
            'receipt_no' => 'nullable|integer|exists:receipts,receipt_no',
        ]);
        
        if ($validated->fails()) {
            return response()->json(['message' => $validated->errors()], 422);
        }
        
        $data = $validated->validated();
        
        // Format the start and end dates
        $startDate = now()->format('Y-m-d');
        if (!empty($data['start_date']) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['start_date'])) {
            $startDate = Carbon::createFromFormat('d/m/Y', $data['start_date'])->format('Y-m-d');
        }
        
        $endDate = now()->addDays(14)->format('Y-m-d');
        if (!empty($data['end_date']) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['end_date'])) {
            $endDate = Carbon::createFromFormat('d/m/Y', $data['end_date'])->format('Y-m-d');
        }
        
        // Calculate grand total from products
        $grand_total = 0;
        if (!empty($data['products'])) {
            foreach ($data['products'] as $product) {
                $productData = Product::find($product['id']);
                if ($productData && $productData->status === 'approved') {
                    $grand_total += $product['price'] * $product['quantity'];
                }
            }
        }
        
        // USD conversion if applicable
        $total_usd = isset($data['exchange_rate']) && $data['exchange_rate'] > 0
            ? $grand_total / $data['exchange_rate']
            : $data['total_usd'] ?? null;
        
        // Set default currency as 'KHR' if not provided
        $currency = $data['currency'] ?? 'KHR';
        
        // Generate invoice number if status is 'Approved'
        $invoice_no = null;
        if ($data['status'] === 'Approved') {
            $currentYear = date('Y');
            $baseInvoiceNo = ($currentYear - 2025) * 1000000 + 25000001;
            $lastInvoice = \App\Models\Invoice::where('invoice_no', '>=', $baseInvoiceNo)
                            ->orderBy('invoice_no', 'desc')
                            ->first();
            $invoice_no = $lastInvoice ? $lastInvoice->invoice_no + 1 : $baseInvoiceNo;
        }
        
        // Set invoice date only if status is Approved
        $invoiceDate = null;
        if ($data['status'] === 'Approved') {
            $invoiceDate = now()->format('Y-m-d H:i:s');
        } elseif ($data['status'] !== 'Pending' && !empty($request->invoice_date) && strtotime($request->invoice_date)) {
            $invoiceDate = Carbon::parse($request->invoice_date)->format('Y-m-d H:i:s');
        }

        $paymentSchedule = PaymentSchedule::find($data['payment_schedule_id']);

        $paid_amount = $data['paid_amount'] ?? 0;
        $installment_paid = 0;
        
        // Create the invoice
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
            'currency'       => $currency,
            'invoice_date'   => $invoiceDate,
            'status'         => $data['status'],
            'paid_amount'    => $paid_amount,
            'payment_schedule_id' => $data['payment_schedule_id'] ?? null,
            'installment_paid' => $installment_paid,
            'receipt_no'         => $data['receipt_no'] ?? null,
        ]);
        
        // Attach products to the pivot table using invoice_id
        if (!empty($data['products'])) {
            foreach ($data['products'] as $product) {
                $productData = Product::find($product['id']);
                if ($productData && $productData->status === 'approved') {
                    $invoice->products()->attach($product['id'], [
                        'quantity' => $product['quantity'],
                        'price' => $product['price'],
                        'include_catalog' => $product['include_catalog'],
                        'pdf_url' => $product['pdf_url'] ?? null,
                        'invoice_id' => $invoice->id,
                    ]);
                }
            }
        }
    
        if (!empty($data['payment_schedules'])) {
            $invoice->paymentSchedules()->attach($data['payment_schedules']);
        }
    
        // Eager load payment schedules with the invoice and return the response
        $invoice = $invoice->load('paymentSchedules');
        
        return redirect()->route('invoices.list')->with('success', 'Invoice created successfully!');
    }
    

public function updateStatus(Request $request, Invoice $invoice)
{   
    // Validate the incoming request
    $validated = $request->validate([
        'status' => 'required|string|in:approved,revise,pending',
        'comment' => 'nullable|string|max:1000',
        'role' => 'nullable|string', // optional
    ]);

    // Update the status of the invoice
    $invoice->status = $validated['status'];

    // When the invoice status is approved
    if ($invoice->status === 'approved') {
        // Generate the invoice number if it doesn't exist
        if (!$invoice->invoice_no) {
            $year = date('Y');
            $baseInvoiceNo = ($year - 2025) * 1000000 + 25000001;
            $lastInvoice = Invoice::where('invoice_no', '>=', $baseInvoiceNo)
                ->orderBy('invoice_no', 'desc')
                ->first();
            $invoice->invoice_no = $lastInvoice ? $lastInvoice->invoice_no + 1 : $baseInvoiceNo;
        }

        // Set the invoice date to the current date
        $invoice->invoice_date = now();
        
        // Automatically set the invoice_end_date to 14 days after the invoice_date
        $invoice->invoice_end_date = Carbon::parse($invoice->invoice_date)->addDays(14)->format('Y-m-d'); // 14 days after invoice_date

        // Set the customer status to 'Sent'
        $invoice->customer_status = 'Sent'; 
    }

    // Save the updated invoice to the database
    $invoice->save();

    // If there is a comment, create an invoice comment
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

    $query->where('status', 'approved');

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
    return Inertia::render('Invoices/List', [
        'invoices' => $invoices,
        'filters' => $filters,
    ]);
}

public function show($invoice_no)
{
    // Load the invoice with customer
    $invoice = Invoice::with('customer')->where('id', $invoice_no)->firstOrFail();

    if ($invoice->status === 'Approved') {
        abort(403, 'This invoice has been paid and can no longer be modified.');
    }

    $products = $invoice->products()->get(); 

    // Get products manually via invoice_product
    $products = DB::table('invoice_product')
    ->join('products', 'invoice_product.product_id', '=', 'products.id')
    ->where('invoice_product.invoice_id', $invoice->id)
    ->select(
        'products.id as product_id',
        'products.name as product_name',
        'products.name_kh as product_name_kh',
        'products.code as product_code',
        'products.desc',      
        'products.desc_kh',   
        'invoice_product.quantity',
        'invoice_product.price',
        'invoice_product.include_catalog'
    )
    ->get();


    return Inertia::render('Invoices/Print', [
        'invoice' => [
            'id' => $invoice->id,
            'invoice_no' => $invoice->invoice_no,
            'invoice_date' => $invoice->invoice_date
                ? Carbon::parse($invoice->invoice_date)->format('Y-m-d')
                : null,
            'due_date' => $invoice->due_date
                ? Carbon::parse($invoice->due_date)->format('Y-m-d')
                : null,
            'customer_id' => $invoice->customer_id,
            'customer_name' => $invoice->customer->name,
            'customer_name_kh' => $invoice->customer->name_kh ?? $invoice->customer->name,
            'address' => $invoice->address ?? $invoice->customer->address,
            'phone_number' => $invoice->phone_number ?? $invoice->customer->phone_number,
            'email' => $invoice->email ?? $invoice->customer->email,
            'products' => $products,
            'grand_total' => $invoice->grand_total,
            'total_usd' => $invoice->total_usd,
            'exchange_rate' => $invoice->exchange_rate,
            'terms' => $invoice->terms ?? 'Standard payment terms apply',
            'status' => $invoice->status,
            'payment_status' => $invoice->payment_status,
            'authorized_by' => $invoice->authorized_by,
            'accepted_by' => $invoice->accepted_by,
            'quotation_no' => $invoice->quotation_no,
            'agreement_no' => $invoice->agreement_no,
            'acceptance_date' => $invoice->acceptance_date
                ? Carbon::parse($invoice->acceptance_date)->format('Y-m-d')
                : null,
        ],
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

    public function getInvoicesByQuotation($quotation_no)
    {
        // Fetch invoices for the given quotation_no and status=paid
        $invoices = Invoice::where('quotation_no', $quotation_no)
                            ->where('status', 'paid')
                            ->get();

        // Return the invoices as a JSON response
        return response()->json($invoices);
    }

    public function updateCustomerStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_status' => 'required|in:Sent,Pending,Accept,Reject',
            'comment' => 'nullable|string',
        ]);

        $quotation = Quotation::findOrFail($id);

        $quotation->update([
            'customer_status' => $validated['customer_status'],
        ]);

        if ($request->has('comment')) {
            // Save comment logic here
            $quotation->comments()->create([
                'comment' => $validated['comment'],
                // 'role' => auth()->user()->role, // or get role from request
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function sendInvoice(Request $request)
{
    // Find the invoice by its ID
    $invoice = Invoice::with('customer')->find($request->input('invoice_id'));

    if (!$invoice) {
        return response()->json(['error' => 'Invoice not found'], 404);
    }

    try {
        // Email validation (only if sending email)
        if ($request->input('send_email')) {
            $customerEmail = $invoice->customer->email;
            if (!$customerEmail) {
                throw new Exception('Customer email not found.');
            }
        }

        // PDF handling
        if ($request->hasFile('pdf_file')) {
            // Save the PDF file in the public storage
            $pdfPath = $request->file('pdf_file')->store('invoices', 'public');
            Log::info('Invoice PDF saved to: ' . $pdfPath);
        } else {
            throw new Exception('Invoice PDF file not found.');
        }

        // Email sending
        if ($request->input('send_email')) {
            Log::info('Sending email to: ' . $customerEmail);
            Mail::to($customerEmail)->send(new InvoiceEmail($invoice, $request->file('pdf_file')));

            // Automatically update statuses when sending email
            $invoice->customer_status = 'Pending'; // Change the invoice status to 'Sent'
            $invoice->customer->update(['customer_status' => 'Pending']); // Update customer status to Pending
        }

        // Save the invoice (status remains unless changed elsewhere)
        $invoice->save();

        return  redirect()->route('invoices.list')->with('success', 'Invoice email sent successfully!');
    } catch (Exception $e) {
        Log::error('Failed to send invoice: ' . $e->getMessage());
        return;
    }
}

public function updatePaymentStatus($invoiceId, Request $request)
{
    $invoice = Invoice::findOrFail($invoiceId);

    $validated = $request->validate([
        'payment_status' => 'required|in:Fully Paid,Partially Paid,Pending,Overdue,Cancelled',
    ]);

    $invoice->payment_status = $validated['payment_status'];
    $invoice->save();

    return response()->json($invoice);
}


}