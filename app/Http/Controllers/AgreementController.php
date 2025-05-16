<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Customer;
use App\Models\PaymentSchedule;
use App\Models\Invoice;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AgreementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agreements = Agreement::with(['customer', 'paymentSchedules.receipts', 'invoices.receipts'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($agreement) {
                // Calculate total paid amount from payment schedules (based on receipts)
                $totalPaid = 0;
                $paymentSchedules = $agreement->paymentSchedules->map(function ($schedule) use ($agreement, &$totalPaid) {
                    // Calculate the total paid amount for each schedule
                    $paidAmount = $schedule->receipts->sum('paid_amount');
                    $totalPaid += $paidAmount;

                    $dueAmount = $schedule->amount - $paidAmount;

                    return [
                        ...$schedule->toArray(),
                        'paid_amount' => $paidAmount,
                        'amount' => $dueAmount,
                        'is_past_due' => $dueAmount > 0 && \Carbon\Carbon::createFromFormat('d/m/Y', $schedule->due_date)->isPast(),
                    ];
                });

                // Calculate total due payment for past-due schedules with unpaid amounts
                $duePayment = $paymentSchedules->sum(function ($schedule) {
                    if ($schedule['is_past_due'] && $schedule['amount'] > 0) {
                        return $schedule['amount'];
                    }
                    return 0;
                });

                // Calculate the total progress payment percentage
                $paymentPercentage = $agreement->amount > 0 ? ($totalPaid / $agreement->amount) * 100 : 0;

                // Prepare progress payments with invoice numbers
                $progressPayments = $agreement->invoices->flatMap(function ($invoice) {
                    return $invoice->receipts->map(function ($receipt) use ($invoice) {
                        return [
                            'receipt_no' => $receipt->receipt_no,
                            'amount' => $receipt->paid_amount,
                            'date' => $receipt->receipt_date,
                            'payment_schedule_id' => $receipt->payment_schedule_id,
                            'invoice_no' => $invoice->invoice_no, // Add invoice number
                            'invoice_id' => $invoice->id, // Add invoice ID for linking
                        ];
                    });
                });

                return [
                    ...$agreement->toArray(),
                    'payment_schedules' => $paymentSchedules,
                    'due_payment' => $duePayment,
                    'total_progress_payment' => $totalPaid,
                    'total_progress_payment_percentage' => $paymentPercentage,
                    'progress_payments' => $progressPayments,

                ];
            });

        return Inertia::render('Agreements/Index', [
            'agreements' => $agreements
        ]);
    }

    protected function determineAgreementStatus($agreement)
    {
        $today = now();
        $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $agreement->end_date);

        if ($today->gt($endDate)) {
            // Check if all payments are completed
            $completedPayments = $agreement->paymentSchedules()
                ->where('status', 'Completed')
                ->count();
            $totalPayments = $agreement->paymentSchedules()->count();

            if ($completedPayments === $totalPayments) {
                return 'Closed';
            } else {
                return 'Abnormal Closed';
            }
        }

        return 'Open';
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $baseAgreementNo = 25000000;
        return Inertia::render('Agreements/Create',[
            'customers' => Customer::where('active', true)->get(),
            'agreement_max' => (Agreement::max('agreement_no') ?? $baseAgreementNo) + 1,
            'csrf_token' => csrf_token(),
            'quotations' => Quotation::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'agreement_no' => 'required',
            'agreement_ref_no' => 'nullable|unique:agreements,agreement_ref_no',
            'agreement_doc' => 'required|array|min:1',
            'agreement_date' => 'required|date_format:d/m/Y',
            'payment_schedule' => 'required|array|min:1',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y',
            'customer_id' => 'required',
            'currency' => 'required',
            'quotation_no' => 'nullable',
        ]);
        // catch exchange_rate from quotations
        $quotation = Quotation::where('quotation_no', $request->quotation_no)->first();
        $exchangeRate = 4100; // default
        if ($request->quotation_no) {
            $quotation = Quotation::where('quotation_no', $request->quotation_no)->first();
            $exchangeRate = $quotation?->exchange_rate ?? 4100;
        }

        $data = $request->except('payment_schedule')+[
            'amount' => $request->agreement_amount,
            'agreement_ref_no' => $request->agreement_ref_no
        ];
      // Ensure agreement_doc is an array or object
        $data['agreement_doc'] = json_encode(
                collect($request->agreement_doc)->map(function ($doc) {
                    return [
                        'path' => $doc['path'] ?? null,
                        'name' => $doc['name'] ?? null,
                        'size' => $doc['size'] ?? null,
                    ];
                })->toArray()
            );
        // Handle attachments (ensure empty array if null)
        $data['attachments'] = json_encode(
            collect($request->attachments ?? [])->map(function ($file) {
                return [
                    'path' => $file['path'] ?? null,
                    'name' => $file['name'] ?? null,
                    'size' => $file['size'] ?? null,
                ];
            })->toArray()
        );
        $agreement = Agreement::create($data);
        foreach($request->payment_schedule as $key => $value){
            unset($value['id']);
            $schedule = new PaymentSchedule([
                'agreement_no' => $request->agreement_no,
                'due_date' => $value['due_date'],
                'amount' => $value['amount'],
                'status' => 'UPCOMING',
                'percentage' => $value['percentage'],
                'short_description' => $value['short_description'],
                'currency' => $value['currency'],
                'exchange_rate' => $value['exchange_rate'] ?? ($value['currency'] === 'KHR' ? $exchangeRate : 1),
            ]);
            $schedule->save();
            // $value['agreement_no'] = $request->agreement_no;
        }
        return to_route('agreements.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {

        $agreement = Agreement::with(['customer',
        'paymentSchedules:id,agreement_no,amount,due_date,status,percentage,short_description,currency'
        ])->findOrFail($id);
        return response()->json([
            ...$agreement->toArray(),
            'agreement_doc' => json_decode($agreement->agreement_doc ?? '[]', true),
            'attachments' => json_decode($agreement->attachments ?? '[]', true),
            'payment_schedules' => $agreement->paymentSchedules->toArray(),
        ]);
    }

    public function print(int $id)
    {
        return Inertia::render('Agreements/Print', [
            'agreement' => Agreement::with('customer')->with('paymentSchedules')->find($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $agreement_no)
    {
        $agreement = Agreement::with([
            'customer',
            'quotation',
            'paymentSchedules:id,agreement_no,amount,due_date,status,percentage,short_description,currency' // include fields you want
        ])->findOrFail($agreement_no);

        $customers = Customer::where('active', true)
            ->orWhere('id', $agreement->customer_id)
            ->get();

        return Inertia::render('Agreements/Edit', [
            'agreement' => $agreement,
            'customers' => $customers,
            'quotations' => Quotation::all(),
            'csrf_token' => csrf_token(), // ✅ required for upload
            // dd("Hello")
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $agreement_no)
    {
        // dd($request->all());
        $request->validate([
            'agreement_no' => 'required',
            'agreement_ref_no' => 'nullable|unique:agreements,agreement_ref_no,',
            'agreement_doc' => 'required',
            'agreement_date' => 'required|date_format:d/m/Y',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y',
            'customer_id' => 'required',
            'currency' => 'required',
            'agreement_ref_no' => 'nullable|string|unique:agreements,agreement_ref_no,'.$agreement_no.',agreement_no',
            'payment_schedule' => 'required|array|min:1',
            'payment_schedule.*.due_date' => 'required|date_format:d/m/Y',
            'payment_schedule.*.amount' => 'required|numeric|min:0',
            'payment_schedule.*.percentage' => 'required|numeric|min:0|max:100',
        ]);

        // catch exchange rate from quotations
        $quotation = Quotation::where('quotation_no', $request->quotation_no)->first();
        $exchangeRate = $quotation?->exchange_rate ?? 4100;

        // Find the agreement
        $agreement = Agreement::where('agreement_no', $agreement_no)->firstOrFail();

        // Prepare data for update
        $data = $request->only([
            'agreement_no',
            'agreement_ref_no',
            'agreement_date',
            'customer_id',
            'address',
            'start_date',
            'end_date',
            'short_description',
            'currency'
        ]);

        $data['amount'] = $request->agreement_amount;
        // $data['agreement_doc'] = json_encode($request->agreement_doc);
        $data['agreement_doc'] = $request->agreement_doc;
        $data['attachments'] = $request->attachments;

        // Update the agreement
        $agreement->update($data);

        // Delete existing payment schedules
        $agreement->paymentSchedules()->delete();

        // Create new payment schedules
        foreach ($request->payment_schedule as $schedule) {
            PaymentSchedule::create([
                'agreement_no' => $agreement->agreement_no,
                'due_date' => $schedule['due_date'],
                'amount' => $schedule['amount'],
                'status' => $schedule['status'],
                'percentage' => $schedule['percentage'],
                'short_description' => $schedule['short_description'],
                'remark' => $schedule['remark'] ?? null,
                'currency' => $schedule['currency'],
                'exchange_rate' => $schedule['exchange_rate'] ?? ($schedule['currency'] === 'KHR' ? 4100 : 1),
            ]);
        }

        return redirect()->route('agreements.index')
            ->with('success', 'Agreement updated successfully');
    }

    /**
     * Upload the specified resource into storage.
     */
    // public function upload(Request $request)
    // {
    //     if($request->has('agreement_doc')){
    //         if($request->has('agreement_doc_old')){
    //             Storage::disk('public')->delete('agreements/'.basename($request->agreement_doc_old));
    //         }
    //         return Storage::url($request->file('agreement_doc')->storePublicly('agreements','public'));
    //     }else if($request->has('attachments')){
    //         return Storage::url($request->file('attachments')->storePublicly('attachments','public'));
    //     }
    // }
    public function upload(Request $request)
    {
        if ($request->hasFile('agreement_doc')) {
            $files = $request->file('agreement_doc');

            // If only one file is uploaded (non-array), convert to array
            if (!is_array($files)) {
                $files = [$files];
            }

            $uploaded = [];

            foreach ($files as $file) {
                $path = $file->storePublicly('agreements', 'public');
                $uploaded[] = [
                    'path' => Storage::url($path),
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }

            return response()->json($uploaded); // return array
        }

        if ($request->hasFile('attachments')) {
            $file = $request->file('attachments');
            $path = $file->storePublicly('attachments', 'public');

            return response()->json([
                'path' => Storage::url($path),
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agreement $agreement)
    {
        //
    }

    // Assuming you have a controller method like this
    public function getAgreementForQuotation($quotationId)
    {
        // Get the quotation with the related agreement
        $quotation = Quotation::with('agreement')->find($quotationId);

        // Check if agreement exists
        if ($quotation && $quotation->agreement) {
            return response()->json($quotation->agreement);
        }

        return response()->json(null);
    }

    public function searchQuotation(Request $request)
    {
        $request->validate([
            'quotation_no' => 'required|numeric'
        ]);

        $quotation = Quotation::where('quotation_no', $request->quotation_no)
                              ->where('active', true)
                              ->whereDoesntHave('agreement')
                              ->with('customer')
                              ->first();

        return response()->json([
            'customer_name' => $quotation->customer->name,
            'address' => $quotation->customer->address,
            'customer_id' => $quotation->customer->id,
            'agreement_amount' => $quotation->total,
            'currency' => $quotation->currency,
            'exchange_rate' => $quotation->exchange_rate,
        ]);
    }

    public function checkDuplicateReference(Request $request)
    {
        $request->validate([
            'reference_no' => 'required|string'
        ]);

        $exists = Agreement::where('agreement_ref_no', $request->reference_no)
            ->when($request->has('exclude_id'), function($query) use ($request) {
                $query->where('id', '!=', $request->exclude_id);
            })
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    public function getAgreementWithPayments($agreementId)
    {
        $agreement = Agreement::with('paymentSchedules')->find($agreementId);

        return response()->json([
            'agreement' => $agreement,
            'payment_schedules' => $agreement->paymentSchedules
        ]);
    }

    public function getPaymentSchedulesForInvoice($invoiceId)
    {
        // Get the invoice by its ID
        $invoice = Invoice::with('agreement.paymentSchedules')->find($invoiceId);

        // Check if invoice exists and if associated agreement has payment schedules
        if ($invoice && $invoice->agreement && $invoice->agreement->paymentSchedules->isNotEmpty()) {
            return response()->json([
                'payment_schedules' => $invoice->agreement->paymentSchedules,
            ]);
        }

        return response()->json(['error' => 'No payment schedules found'], 404);
    }


}
