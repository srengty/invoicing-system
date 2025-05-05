<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\CustomerCategory;
use App\Models\PaymentSchedule;
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

        $invoices = Invoice::with(['customer', 'paymentSchedule'])
            ->select('id', 'invoice_no', 'grand_total', 'paid_amount', 'currency', 'customer_id','payment_schedule_id')
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
                $invoice->payment_schedule_id = $invoice->paymentSchedule?->id ?? null;

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
            'customer_code' => 'nullable|string',
            'amount_in_words' => 'nullable|string',
            'payment_method' => 'required|string|in:Cash,Bank Transfer,Credit Card',
            'payment_reference_no' => 'nullable|string',
            'purpose' => 'nullable|string',
            'paid_amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->payment_schedule_id) {
                        $schedule = PaymentSchedule::find($request->payment_schedule_id);
                        if (!$schedule) {
                            $fail('Invalid payment schedule');
                            return;
                        }
                        $remaining = $schedule->amount - ($schedule->paid_amount ?? 0);
                        if ($value > $remaining) {
                            $fail("The paid amount cannot exceed the remaining amount of ".number_format($remaining, 2));
                        }
                    }
                },
            ],
            'invoice_no' => 'nullable|regex:/^\d+$/|exists:invoices,invoice_no',
            'payment_schedule_id' => 'nullable|exists:payment_schedules,id',
        ]);

        // Check if invoice already has a receipt (only if invoice_no is provided)
        if (!empty($validated['invoice_no'])) {
            $existingReceipt = Receipt::where('invoice_no', $validated['invoice_no'])->first();
            if ($existingReceipt) {
                return response()->json([
                    'success' => false,
                    'message' => 'This invoice already has a receipt',
                ], 422);
            }
        }

        $customer = Customer::find($validated['customer_id']);
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        $data = [
            'invoice_no' => $validated['invoice_no'] ?? null,
            'receipt_no' => $validated['receipt_no'],
            'receipt_date' => $validated['receipt_date'],
            'customer_id' => $validated['customer_id'],
            'customer_code' => $customer->code,
            'purpose' => $validated['purpose'] ?? null,
            'paid_amount' => $validated['paid_amount'],
            'amount_in_words' => $this->convertToWords($validated['paid_amount']),
            'payment_method' => $validated['payment_method'],
            'payment_reference_no' => $validated['payment_reference_no'] ?? null,
            'payment_schedule_id' => $validated['payment_schedule_id'] ?? null, // Add this line
        ];

        try {
            $receipt = Receipt::create($data);

             // Update invoice if provided
            if (!empty($validated['invoice_no'])) {
                $invoice = Invoice::where('invoice_no', $validated['invoice_no'])->first();
                if ($invoice) {
                    $invoice->increment('paid_amount', $validated['paid_amount']);
                }
            }

            // Update payment schedule if provided
            if (!empty($validated['payment_schedule_id'])) {
                $paymentSchedule = PaymentSchedule::find($validated['payment_schedule_id']);
                if ($paymentSchedule) {
                    // Calculate new paid amount
                    $newPaidAmount = ($paymentSchedule->paid_amount ?? 0) + $validated['paid_amount'];

                    // Update payment schedule
                    $paymentSchedule->update([
                        'paid_amount' => $newPaidAmount,
                        'status' => $newPaidAmount >= $paymentSchedule->amount ? 'PAID' : 'PARTIALLY_PAID'
                    ]);
                }
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Receipt created successfully',
                'receipt' => $receipt->load(['customer', 'invoice', 'paymentSchedule'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create receipt: ' . $e->getMessage(),
            ], 500);
        }
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
        $receipt = Receipt::with(['invoice', 'customer'])->where('receipt_no', $receipt_no)->firstOrFail();

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
    public function update(Request $request, $receipt_no)
    {
        $receipt = Receipt::where('receipt_no', (string)$receipt_no)->first();

        if (!$receipt) {
            return response()->json([
                'success' => false,
                'message' => "Receipt not found",
            ], 404);
        }

        // Validate input
        $validated = $request->validate([
            'receipt_no' => 'required|string|unique:receipts,receipt_no,'.$receipt_no.',receipt_no',
            'receipt_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'customer_code' => 'nullable|string',
            'purpose' => 'nullable|string',
            'paid_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:Cash,Bank Transfer,Credit Card',
            'payment_reference_no' => 'nullable|string',
            'invoice_no' => 'nullable|string|exists:invoices,invoice_no',
            'payment_schedule_id' => 'nullable|exists:payment_schedules,id',
        ]);

        // Check for duplicate invoice
        if ($request->invoice_no && $request->invoice_no !== $receipt->invoice_no) {
            $existingReceipt = Receipt::where('invoice_no', $request->invoice_no)
                ->where('receipt_no', '!=', $receipt_no)
                ->exists();

            if ($existingReceipt) {
                return response()->json([
                    'success' => false,
                    'message' => 'This invoice already has a receipt',
                ], 422);
            }
        }

        // Update receipt
        $customer = Customer::findOrFail($validated['customer_id']);

        $receipt->update([
            'invoice_no' => $validated['invoice_no'] ?? null,
            'receipt_no' => $validated['receipt_no'],
            'receipt_date' => $validated['receipt_date'],
            'customer_id' => $validated['customer_id'],
            'customer_code' => $customer->code,
            'purpose' => $validated['purpose'] ?? null,
            'paid_amount' => $validated['paid_amount'],
            'amount_in_words' => $this->convertToWords($validated['paid_amount']),
            'payment_method' => $validated['payment_method'],
            'payment_reference_no' => $validated['payment_reference_no'] ?? null,
            'payment_schedule_id' => $validated['payment_schedule_id'] ?? null,
        ]);

        // Update the corresponding payment schedule
        // $paymentSchedule = PaymentSchedule::findOrFail($validated['payment_schedule_id']);
        // $paymentSchedule->paid_amount += $validated['paid_amount']; // Update the paid amount
        // $paymentSchedule->updateStatus(); // Update the status of the payment schedule

         // Update payment schedule if provided
        if (!empty($validated['payment_schedule_id'])) {
            $paymentSchedule = PaymentSchedule::find($validated['payment_schedule_id']);
            if ($paymentSchedule) {
                $paymentSchedule->paid_amount = ($paymentSchedule->paid_amount ?? 0) + $validated['paid_amount'];
                $paymentSchedule->updateStatus();
                $paymentSchedule->save();
            }
        }

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
        $currentYear = date('y');
        $baseNumber = (int) ($currentYear . '000001');

        if (!$latestReceipt) {
            return $baseNumber;
        }

        $lastYear = (int) substr($latestReceipt->receipt_no, 0, 2);

        if ($lastYear != $currentYear) {
            return $baseNumber;
        }

        return $latestReceipt->receipt_no + 1;
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
