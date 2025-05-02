<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\CustomerCategory;
use Illuminate\Http\Request;
use NumberFormatter;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

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

        $invoices = Invoice::with('customer')
            ->select('id', 'invoice_no', 'grand_total', 'paid_amount', 'currency', 'customer_id')
            ->where('status', 'Approved')
            ->whereNotIn('invoice_no', function($query) {
                $query->select('invoice_no')
                      ->from('receipts')
                      ->whereNotNull('invoice_no');
            })
            ->get()
            ->map(function ($invoice) {
                // $invoice->remaining_amount = $invoice->grand_total - ($invoice->paid_amount ?? 0);
                $invoice->customer_name = $invoice->customer?->name ?? null;
                $invoice->customer_code = $invoice->customer?->code ?? null;

                return $invoice;
            });

        return response()->json([
            'nextReceiptNo' => $nextReceiptNo,
            'invoices' => $invoices,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receipt_no' => 'required|string|unique:receipts',
            'receipt_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'customer_code' => 'required|string',
            'amount_in_words' => 'nullable|string',
            'payment_method' => 'nullable|string|in:Cash,Bank Transfer,Credit Card',
            'payment_reference_no' => 'nullable|string',
            'purpose' => 'nullable|string',
            'invoice_no' => 'nullable|regex:/^\d+$/|exists:invoices,invoice_no',
            'paid_amount' => 'nullable|numeric|min:0.01',
        ]);
        $lastReceipt = Receipt::orderBy('receipt_no', 'desc')->first();
        $receiptNo = $this->generateReceiptNumber($lastReceipt);

        $customer = Customer::findOrFail($request->customer_id);

        // $invoice = null;
        // if ($validated['invoice_no']) {
        //     $invoice = Invoice::where('invoice_no', $validated['invoice_no'])->first();

        //     // Update the invoice's paid amount
        //     if ($invoice) {
        //         $newPaidAmount = ($invoice->paid_amount ?? 0) + $validated['paid_amount'];
        //         $invoice->update([
        //             'paid_amount' => $newPaidAmount,
        //             'payment_status' => $newPaidAmount >= $invoice->grand_total
        //                 ? 'Fully Paid'
        //                 : 'Partially Paid'
        //         ]);
        //     }
        // }

        if ($validated['invoice_no']) {
            // Check if the invoice already has a receipt
            $existingReceipt = Receipt::where('invoice_no', $validated['invoice_no'])->first();
            if ($existingReceipt) {
                return response()->json([
                    'success' => false,
                    'message' => 'This invoice already has a receipt',
                ], 422);
            }
        }

        $data = [
            'invoice_no' => $request->invoice_no,
            'receipt_no' => $receiptNo,
            'receipt_date' => $request->receipt_date,
            'customer_id' => $request->customer_id,
            'customer_code' => $customer->code,
            'purpose' => $request->purpose,
            'paid_amount' => $request->paid_amount,
            'amount_in_words' => $this->convertToWords($request->paid_amount),
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

    protected function generateNextReceiptNumber($currentReceiptNo)
    {
        // Assuming receipt_no is numeric and increments by 1
        $numericPart = (int) preg_replace('/[^0-9]/', '', $currentReceiptNo);
        return str_pad($numericPart + 1, strlen($currentReceiptNo), '0', STR_PAD_LEFT);
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
            'customer_code' => 'required|string',
            'paid_amount' => 'required|numeric',
            'payment_method' => 'required|string',
            'payment_reference_no' => 'nullable|string',
            'purpose' => 'nullable|string',
        ]);

         // Check if invoice is being changed and validate it
         if ($request->has('invoice_no') && $request->invoice_no !== $receipt->invoice_no) {
            if ($request->invoice_no) {
                $existingReceipt = Receipt::where('invoice_no', $request->invoice_no)
                    ->where('id', '!=', $id)
                    ->first();

                if ($existingReceipt) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This invoice already has a receipt',
                    ], 422);
                }
            }

            // If removing invoice association, set to null
            $validated['invoice_no'] = $request->invoice_no ?: null;
        }

        $customer = Customer::findOrFail($validated['customer_id']);

        $receipt->update([
            'invoice_no' => $validated['invoice_no'],
            'receipt_no' => $validated['receipt_no'],
            'receipt_date' => $validated['receipt_date'],
            'customer_id' => $validated['customer_id'],
            'customer_code' => $customer->code,
            'purpose' => $validated['purpose'] ?? null,
            'paid_amount' => $validated['paid_amount'],
            'amount_in_words' => $this->convertToWords($validated['paid_amount']),
            'payment_method' => $validated['payment_method'],
            'payment_reference_no' => $validated['payment_reference_no'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Receipt updated successfully',
            'receipt' => $receipt->fresh()
        ]);
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
