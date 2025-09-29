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
use Illuminate\Support\Facades\Log;


class ReceiptController extends Controller
{
    public function index()
    {
        $receipts = Receipt::with(['customer', 'invoices'])
            ->orderBy('receipt_no', 'desc')
            ->get();

        return Inertia::render('Receipts/Index', [
            'receipts' => $receipts,
        ]);
    }

    public function create()
    {
        $lastReceipt = Receipt::orderBy('receipt_no', 'desc')->first();
        $nextReceiptNo = $this->generateReceiptNumber($lastReceipt);

        $invoices = Invoice::with(['customer', 'paymentSchedules'])
            ->select('id', 'invoice_no', 'grand_total', 'paid_amount', 'currency', 'customer_id')
            ->where('status', 'Approved')
            ->whereRaw('grand_total > paid_amount')
            ->get()
            ->map(function ($invoice) {
                $invoice->customer_name = $invoice->customer?->name ?? null;
                $invoice->customer_code = $invoice->customer?->code ?? null;
                $invoice->payment_schedule_ids = $invoice->paymentSchedules->pluck('id')->toArray();
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
                    if (!empty($request->invoices) && is_array($request->invoices) && count($request->invoices) > 0) {
                        $invoices = \App\Models\Invoice::whereIn('invoice_no', $request->invoices)->get();

                        // Check all invoices belong to same customer
                        $customerIds = $invoices->pluck('customer_id')->unique();
                        if ($customerIds->count() > 1) {
                            $fail("All invoices must belong to the same customer.");
                            return;
                        }

                        // Check all invoices belong to same agreement
                        $agreementNos = $invoices->pluck('agreement_no')->unique();
                        if ($agreementNos->count() > 1) {
                            $fail("All invoices must belong to the same agreement.");
                            return;
                        }

                        // Check total remaining balance
                        $totalRemaining = 0;
                        foreach ($invoices as $invoice) {
                            $remaining = $invoice->grand_total - $invoice->paid_amount;
                            $totalRemaining += $remaining;
                        }

                        if ($totalRemaining > 0 && $value > $totalRemaining) {
                            $fail("The paid amount cannot exceed the total remaining invoice balance of " . number_format($totalRemaining, 2));
                        }
                    }
                },
            ],
            'invoices' => 'nullable|array|min:1',
            'invoices.*' => 'nullable|exists:invoices,invoice_no',
            'payment_schedule_ids' => 'nullable|array',
            'payment_schedule_ids.*' => 'exists:payment_schedules,id',
        ]);

        $customer = Customer::find($validated['customer_id']);
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer not found.');
        }

        // Validate payment schedules and invoices agreement/customer consistency
        if (!empty($validated['payment_schedule_ids'])) {
            $schedules = \App\Models\PaymentSchedule::whereIn('id', $validated['payment_schedule_ids'])->get();
            $agreementIds = $schedules->pluck('agreement_id')->unique();

            if ($agreementIds->count() > 1) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['payment_schedule_ids' => 'All payment schedules must belong to the same agreement.']);
            }

            $agreementId = $agreementIds->first();

            if (!empty($validated['invoices'])) {
                $invoices = \App\Models\Invoice::whereIn('invoice_no', $validated['invoices'])->get();
                $invoiceAgreementIds = $invoices->pluck('agreement_id')->unique();

                if ($invoiceAgreementIds->count() > 1 || !$invoiceAgreementIds->contains($agreementId)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['invoices' => 'All invoices must belong to the same agreement as the payment schedules.']);
                }

                // Optional: Check customer consistency
                $invoiceCustomerIds = $invoices->pluck('customer_id')->unique();
                $scheduleCustomerId = $schedules->first()->agreement->customer_id;

                if ($invoiceCustomerIds->count() > 1 || $invoiceCustomerIds->first() != $scheduleCustomerId) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['customer' => 'Invoices and payment schedules must belong to the same customer.']);
                }
            }
        }

        // Create receipt
        $receipt = Receipt::create([
            'receipt_no' => $validated['receipt_no'],
            'receipt_date' => $validated['receipt_date'],
            'customer_id' => $validated['customer_id'],
            'customer_code' => $customer->code,
            'paid_amount' => $validated['paid_amount'],
            'amount_in_words' => $this->convertToWords($validated['paid_amount']),
            'payment_method' => $validated['payment_method'],
            'payment_reference_no' => $request->input('payment_reference_no'),
            'installment_paid' => 0,
        ]);

        /**
         * 🔹 Process invoices (distribute payments and absorb old installment_paid)
         */
        if (!empty($validated['invoices'])) {
            $invoices = Invoice::whereIn('invoice_no', $validated['invoices'])->get();
            $receipt->invoices()->sync($invoices->pluck('id'));

            $remainingPaid = $validated['paid_amount'];

            foreach ($invoices as $invoice) {
                // Step 1: absorb old installment into paid_amount
                if ($invoice->installment_paid > 0) {
                    $invoice->paid_amount += $invoice->installment_paid;
                    $invoice->installment_paid = 0;
                }

                if ($remainingPaid <= 0) break;

                // Step 2: calculate how much is due now
                $invoiceDue = $invoice->grand_total - $invoice->paid_amount;

                if ($remainingPaid >= $invoiceDue) {
                    // Fully settle invoice
                    $invoice->paid_amount += $invoiceDue;
                    $remainingPaid -= $invoiceDue;
                } else {
                    // Partial payment
                    $invoice->paid_amount += $remainingPaid;
                    $invoice->installment_paid = $invoiceDue - $remainingPaid;
                    $remainingPaid = 0;
                }

                $invoice->receipt_no = $receipt->receipt_no;
                $invoice->save();
            }

            // Any leftover not applied to invoices is stored as receipt installment
            $receipt->installment_paid = $remainingPaid;
            $receipt->save();
        }

        /**
         * 🔹 Process payment schedules
         */
        if (!empty($validated['payment_schedule_ids'])) {
            $remainingAmount = $validated['paid_amount'];

            foreach ($validated['payment_schedule_ids'] as $scheduleId) {
                $schedule = PaymentSchedule::find($scheduleId);
                if ($schedule && $remainingAmount > 0) {
                    $dueAmount = $schedule->amount - $schedule->paid_amount;
                    $appliedAmount = min($remainingAmount, $dueAmount);

                    $schedule->paid_amount += $appliedAmount;
                    $schedule->status = $this->determinePaymentStatus($schedule);
                    $schedule->save();

                    $receipt->paymentSchedules()->syncWithoutDetaching([$scheduleId]);

                    $remainingAmount -= $appliedAmount;
                }
            }
        }

        return redirect()->route('receipts.index')->with('success', 'Receipt created successfully!');
    }

    private function determinePaymentStatus(PaymentSchedule $schedule)
    {
        if ($schedule->paid_amount >= $schedule->amount) {
            return 'PAID';
        } elseif ($schedule->paid_amount > 0) {
            return 'PARTIALLY_PAID';
        }
        return 'UNPAID';
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
            'receipt_no' => 'required|string|unique:receipts,receipt_no,' . $receipt_no . ',receipt_no',
            'receipt_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|string|in:Cash,Bank Transfer,Credit Card',
            'paid_amount' => 'required|numeric|min:0.01',
            'invoices' => 'required|array|min:1',
            'invoices.*' => 'exists:invoices,invoice_no',
            'payment_schedule_ids' => 'nullable|array',
            'payment_schedule_ids.*' => 'exists:payment_schedules,id',
        ]);

        $customer = Customer::findOrFail($validated['customer_id']);

        $receipt->update([
            'receipt_no' => $validated['receipt_no'],
            'receipt_date' => $validated['receipt_date'],
            'customer_id' => $validated['customer_id'],
            'customer_code' => $customer->code,
            'paid_amount' => $validated['paid_amount'],
            'amount_in_words' => $this->convertToWords($validated['paid_amount']),
            'payment_method' => $validated['payment_method'],
            'payment_reference_no' => $request->input('payment_reference_no'),
        ]);

        // Sync invoices
        $invoiceIds = Invoice::whereIn('invoice_no', $validated['invoices'])->pluck('id')->toArray();
        $receipt->invoices()->sync($invoiceIds);

        // Update payment schedules
        if (!empty($validated['payment_schedule_ids'])) {
            $receipt->paymentSchedules()->sync($validated['payment_schedule_ids']);
            PaymentSchedule::whereIn('id', $validated['payment_schedule_ids'])
                ->update(['status' => 'PIAD']); // or customize logic
        }

        return response()->json([
            'success' => true,
            'message' => 'Receipt updated successfully',
            'receipt' => $receipt->fresh(),
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

    public function availableReceipts()
    {
        $receipts = Receipt::where(function ($q) {
            $q->whereNull('invoice_no')
            ->orWhere('installment_paid', '>', 0);
        })->get();

        return Inertia::render('Invoices/Create', [
            'availableReceipts' => $receipts
        ]);
    }

}