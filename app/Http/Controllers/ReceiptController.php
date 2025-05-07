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
    
        $invoices = Invoice::with(['customer', 'paymentSchedules'])  // Make sure to load the paymentSchedules relation
            ->select('id', 'invoice_no', 'grand_total', 'paid_amount', 'currency', 'customer_id')
            ->where('status', 'Approved')
            ->whereNotIn('invoice_no', function($query) {
                $query->select('invoice_no')
                    ->from('receipts')
                    ->whereNotNull('invoice_no');
            })
            ->get()
            ->map(function ($invoice) {
                $invoice->customer_name = $invoice->customer?->name ?? null;
                $invoice->customer_code = $invoice->customer?->code ?? null;
    
                // Get the IDs of payment schedules for the invoice
                $invoice->payment_schedule_ids = $invoice->paymentSchedules->pluck('id')->toArray(); // Adjusting here
    
                return $invoice;
            });
    
        return response()->json([
            'nextReceiptNo' => $nextReceiptNo,
            'invoices' => $invoices,
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receipt_no' => 'required|string|unique:receipts',
            'receipt_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|string|in:Cash,Bank Transfer,Credit Card',
            'paid_amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($request) {
                    // Validate for each payment schedule in the array
                    if ($request->payment_schedule_ids) {
                        foreach ($request->payment_schedule_ids as $scheduleId) {
                            $schedule = PaymentSchedule::find($scheduleId);
                            if (!$schedule) {
                                $fail('Invalid payment schedule ID');
                                return;
                            }
                            $remaining = $schedule->amount - ($schedule->paid_amount ?? 0);
                            if ($value > $remaining) {
                                $fail("The paid amount cannot exceed the remaining amount of ".number_format($remaining, 2));
                            }
                        }
                    }
                },
            ],
            'invoice_no' => 'nullable|regex:/^\d+$/|exists:invoices,invoice_no',
            'payment_schedule_ids' => 'nullable|array',
            'payment_schedule_ids.*' => 'exists:payment_schedules,id', // Validate each ID in the array
        ]);
    
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
            'paid_amount' => $validated['paid_amount'],
            'amount_in_words' => $this->convertToWords($validated['paid_amount']),
            'payment_method' => $validated['payment_method'],
            'payment_schedule_ids' => $validated['payment_schedule_ids'] ?? [], // Store the payment schedule IDs
        ];
    
        try {
            DB::beginTransaction();  // Add transaction to ensure atomicity
    
            $receipt = Receipt::create($data);
    
            // If invoice is provided, update its paid amount
            if (!empty($validated['invoice_no'])) {
                $invoice = Invoice::where('invoice_no', $validated['invoice_no'])->first();
                if ($invoice) {
                    $invoice->increment('paid_amount', $validated['paid_amount']);
                }
            }
    
            // Update payment schedules if provided
            if ($request->has('payment_schedule_ids')) {
                $validScheduleIds = $request->payment_schedule_ids;
    
                // Attach to receipt (assuming you have a many-to-many relationship)
                $receipt->paymentSchedules()->sync($validScheduleIds);
    
                // Update payment schedule statuses
                PaymentSchedule::whereIn('id', $validScheduleIds)
                    ->update(['status' => 'paid']); // Or your desired logic for the status
            }
    
            DB::commit();  // Commit the transaction
    
            return response()->json([
                'success' => true,
                'message' => 'Receipt created successfully',
                'receipt' => $receipt->load(['customer', 'invoice', 'paymentSchedules'])  // Load paymentSchedules here
            ]);
        } catch (\Exception $e) {
            DB::rollBack();  // Rollback if something goes wrong
            return response()->json([
                'success' => false,
                'message' => 'Failed to create receipt: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function update(Request $request, $receipt_no)
    {
        $receipt = Receipt::where('receipt_no', (string)$receipt_no)->first();
    
        if (!$receipt) {
            return response()->json([
                'success' => false,
                'message' => "Receipt not found",
            ], 404);
        }
    
        $validated = $request->validate([
            'receipt_no' => 'required|string|unique:receipts,receipt_no,'.$receipt_no.',receipt_no',
            'receipt_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|string|in:Cash,Bank Transfer,Credit Card',
            'paid_amount' => 'required|numeric|min:0.01',
            'invoice_no' => 'nullable|string|exists:invoices,invoice_no',
            'payment_schedule_ids' => 'nullable|array',
            'payment_schedule_ids.*' => 'exists:payment_schedules,id',
        ]);
    
        $customer = Customer::findOrFail($validated['customer_id']);
    
        $receipt->update([
            'invoice_no' => $validated['invoice_no'] ?? null,
            'receipt_no' => $validated['receipt_no'],
            'receipt_date' => $validated['receipt_date'],
            'customer_id' => $validated['customer_id'],
            'customer_code' => $customer->code,
            'paid_amount' => $validated['paid_amount'],
            'amount_in_words' => $this->convertToWords($validated['paid_amount']),
            'payment_method' => $validated['payment_method'],
        ]);
    
        // Update the payment schedules
        if (!empty($validated['payment_schedule_ids'])) {
            $paymentScheduleIds = $validated['payment_schedule_ids'];
    
            // Attach to receipt (many-to-many relationship)
            $receipt->paymentSchedules()->sync($paymentScheduleIds);
    
            // Update the statuses of the payment schedules
            PaymentSchedule::whereIn('id', $paymentScheduleIds)
                ->update(['status' => 'paid']);  // Or your desired logic
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Receipt updated successfully',
            'receipt' => $receipt->fresh()
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
    public function show($receipt_no)
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