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
        $nextReceiptNo = $lastReceipt ? (intval($lastReceipt->receipt_no) + 1) : 25000001;
        return response()->json([
            'nextReceiptNo' =>'R' . str_pad($nextReceiptNo, 8, '0', STR_PAD_LEFT),  // Format the number to always have 8 digits
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
            'amount_paid' => 'required|numeric',
            'payment_method' => 'required|string',
            'payment_reference_no' => 'nullable|string',
        ]);

        $lastReceipt = Receipt::orderBy('receipt_no', 'desc')->first();
        $receipt_no = $lastReceipt ? ((int)substr($lastReceipt->receipt_no, 5)) + 1 : 25000001;

        $customer = Customer::findOrFail($request->customer_id);

        $data = [
            'invoice_no' => $request->invoice_no,
            'receipt_no' => 'R' . date('Y') . str_pad($receipt_no, 5, '0', STR_PAD_LEFT),
            'receipt_date' => $request->receipt_date,
            'customer_id' => $request->customer_id,
            'customer_code' => $customer->customer_code,
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
            'receipt_no' => 'required',
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
        $year = date('Y'); // Current year
        $baseNumber = 25000001; // Base number for generating receipts

        // Get the last receipt number for the current year
        if ($latestReceipt) {
            $lastNumber = (int) substr($latestReceipt->receipt_no, 5); // Skip the 'R2025' part
            $baseNumber = $lastNumber + 1;
        }

        // Format the receipt number with leading zeros (e.g., '00001')
        $formattedNumber = str_pad($baseNumber, 5, '0', STR_PAD_LEFT);

        return 'R' . $year . $formattedNumber; // Concatenate the year and formatted number
    }

    /**
     * Convert amount to words
     */
    protected function convertToWords($amount)
    {
        $formatter = new NumberFormatter('en', NumberFormatter::SPELLOUT);
        return ucfirst($formatter->format($amount));
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
