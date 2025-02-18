<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\CustomerCategory;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Invoice;
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
        $quotations = Quotation::with(["products","productQuotations","agreement"])->get();
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

    // Store a newly created invoice
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = Validator::make($request->all(), [
            'invoice_no' => 'required|unique:invoices',
            'agreement_no' => 'required',
            'quotation_no' => 'required',
            'customer_id' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id', 
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        if ($validated->fails()) {
            return response()->json(['message' => $validated->errors()]);
        }

        // Get validated data
        $validated = $validated->validated();

        // Format start_date and end_date using Carbon
        $startDate = Carbon::parse($validated['start_date'])->toDateTimeString();
        $endDate = Carbon::parse($validated['end_date'])->toDateTimeString();

        // Calculate the grand total (without storing the subtotal)
        $grand_total = 0;
        foreach ($validated['products'] as $product) {
            $prod = Product::find($product['id']);
            $grand_total += $prod->price * $product['quantity'];
        }

        // Assuming tax is 10% of the grand total
        $tax = 0.0 * $grand_total;
        $grand_total += $tax;  // Add tax to grand total

        // Create the invoice
        $invoice = Invoice::create([
            'invoice_no' => $validated['invoice_no'],
            'agreement_no' => $validated['agreement_no'],
            'quotation_no' => $validated['quotation_no'],
            'customer_id' => $validated['customer_id'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'grand_total' => $grand_total,  // Store only the grand total
        ]);

        // Attach the selected products with their quantities to the invoice
        foreach ($validated['products'] as $product) {
            $invoice->products()->attach($product['id'], ['quantity' => $product['quantity']]);
        }

        // Redirect with a success message
        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully!');
    }

    // Show the list of invoices
    public function index(Request $request)
    {
        // Initialize the query builder for the invoices
        $query = Invoice::with('customer', 'agreement', 'quotation', 'products', 'customer_category');
        
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
            $query->where('date', '>=', $request->start_date);
        }

        // Filter by end date
        if ($request->has('end_date') && $request->end_date) {
            $query->where('date', '<=', $request->end_date);
        }

        if ($request->has('category_name_english') && $request->category_name_english) {
            $query->whereHas('customer.customerCategory', function ($query) use ($request) {
                $query->where('category_name_english', 'like', '%' . $request->category_name_english . '%');
            });
        }        
        
        // Filter by currency
        if ($request->has('currency') && $request->currency) {
            $query->where('currency', $request->currency);
        }

        // Paginate the results (you can adjust the pagination per page)
        $invoices = $query->paginate();  // Adjust the pagination limit if needed

        $filters = $request->only(['invoice_no_start', 'invoice_no_end', 'category_name_english', 'currency']);

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

    public function show($id)
    {
        $invoice = Invoice::with(['customer', 'products'])->findOrFail($id);

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice
        ]);
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
}
