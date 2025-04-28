<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use NumberFormatter;
use Inertia\Inertia;

class ReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $receipts = Receipt::with(['customer', 'invoice'])
            ->orderBy('receipt_no', 'desc')
            ->get();

        return Inertia::render('Receipts/Index', [
            'receipts' => $receipts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lastReceipt = Receipt::orderBy('receipt_no', 'desc')->first();
        $nextReceiptNo = $this->generateReceiptNumber($lastReceipt);

        // Ensure it's returning a valid number
        return response()->json([
            'nextReceiptNo' => $nextReceiptNo,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_no' => 'nullable|exists:invoices,invoice_no',
            'receipt_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'customer_code' => 'nullable|string|max:255',
            'amount_paid' => 'required|numeric',
            'payment_method' => 'required|string',
            'payment_reference_no' => 'nullable|string',
        ]);

        // Generate receipt_no based on the latest one
        $lastReceipt = Receipt::orderBy('receipt_no', 'desc')->first();
        $receiptNo = $this->generateReceiptNumber($lastReceipt);

        $customer = Customer::findOrFail($request->customer_id);

        $data = [
            'invoice_no' => $request->invoice_no,
            'receipt_no' => $receiptNo,
            'receipt_date' => $request->receipt_date,
            'customer_id' => $request->customer_id,
            'customer_code' => $customer->code,
            'amount_paid' => $request->amount_paid,
            'amount_in_words' => $this->convertToWords($request->amount_paid),
            'payment_method' => $request->payment_method,
            'payment_reference_no' => $request->payment_reference_no,
        ];

        $receipt = Receipt::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Receipt created successfully',
            'receipt' => $receipt
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $receipt = Receipt::with(['invoice', 'customer'])->findOrFail($id);

        return Inertia::render('Receipts/Show', [
            'receipt' => $receipt,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $receipt = Receipt::with(['invoice', 'customer'])->findOrFail($id);
        $invoices = Invoice::all();
        $customers = Customer::all();

        return Inertia::render('Receipts/Edit', [
            'receipt' => $receipt,
            'invoices' => $invoices,
            'customers' => $customers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $receipt = Receipt::findOrFail($id);

        $request->validate([
            'receipt_no' => 'required',
            'invoice_no' => 'nullable|exists:invoices,invoice_no',
            'receipt_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'amount_paid' => 'required|numeric',
            'payment_method' => 'required|string',
            'payment_reference_no' => 'nullable|string',
        ]);

        $receipt->update([
            'receipt_no' => $receipt->receipt_no, // Updated logic keeps the original receipt_no
            'invoice_no' => $request->invoice_no,
            'receipt_date' => $request->receipt_date,
            'customer_id' => $request->customer_id,
            'amount_paid' => $request->amount_paid,
            'amount_in_words' => $this->convertToWords($request->amount_paid),
            'payment_method' => $request->payment_method,
            'payment_reference_no' => $request->payment_reference_no,
        ]);

        return redirect()->route('receipts.index')
            ->with('success', 'Receipt updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $receipt = Receipt::findOrFail($id);
        $receipt->delete();

        return redirect()->route('receipts.index')
            ->with('success', 'Receipt deleted successfully.');
    }

    protected function generateReceiptNumber($latestReceipt)
    {
        $currentYear = date('y'); // Get last 2 digits of the year
        $baseNumber = (int) ($currentYear . '000001'); // Start from 25000001

        if (!$latestReceipt) {
            return $baseNumber; // If no receipts exist, start from base
        }

        $lastYear = (int) substr($latestReceipt->receipt_no, 0, 2); // Extract year portion from receipt_no

        if ($lastYear != $currentYear) {
            return $baseNumber; // Reset sequence if the year has changed
        }

        return $latestReceipt->receipt_no + 1; // Increment receipt number
    }

    /**
     * Convert amount to words
     */
    protected function convertToWords($amount)
    {
        $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
        return ucfirst($formatter->format($amount)); // Convert number to words
    }

    /**
     * Print the specified receipt.
     */
    public function print($id)
    {
        $receipt = Receipt::with(['invoice', 'customer'])->findOrFail($id);

        return Inertia::render('Receipts/Print', [
            'receipt' => $receipt,
        ]);
    }
}
