<?php

namespace App\Http\Controllers;

use App\Models\PaymentSchedule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PaymentScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentSchedule $paymentSchedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentSchedule $paymentSchedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, PaymentSchedule $paymentSchedule)
    // {
    //     //
    // }
    public function update(Request $request, $id)
    {
        // Validate incoming request
        $request->validate([
            'due_date' => 'required|date',
            'short_description' => 'required|string',
            'amount' => 'required|numeric',
            'percentage' => 'required|numeric|min:0|max:100',
            // 'status' => 'required|in:UPCOMING,PAST DUE,PAID', // Validate the status
        ]);

        $paymentSchedule = PaymentSchedule::findOrFail($id);

        $paymentSchedule->update([
            'due_date' => $validated['due_date'],
            'short_description' => $validated['short_description'],
            'amount' => $validated['amount'],
            'percentage' => $validated['percentage'],
        ]);

        $paymentSchedule->refresh()->updateStatus();

        return response()->json([
            'success' => true,
            'message' => 'Payment schedule updated successfully',
            'data' => $paymentSchedule,
        ]);
    }

    // Method to create a receipt and update status
    public function createReceipt(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'payment_schedule_id' => 'required|exists:payment_schedules,id',
                'paid_amount' => 'required|numeric|min:0.01',
                'payment_method' => 'required|string',
                'receipt_date' => 'required|date',
                'receipt_no' => 'required|string|unique:receipts', // Add receipt number validation
                'customer_id' => 'required|exists:customers,id', // Add customer validation
            ]);

            // Create receipt with all required fields
            $receipt = Receipt::create([
                'receipt_no' => $validated['receipt_no'],
                'receipt_date' => $validated['receipt_date'],
                'customer_id' => $validated['customer_id'],
                'paid_amount' => $validated['paid_amount'],
                'payment_method' => $validated['payment_method'],
                'payment_schedule_id' => $validated['payment_schedule_id'], // This is critical
                // Add other required fields as needed
            ]);

            // Update payment schedule
            $paymentSchedule = PaymentSchedule::findOrFail($validated['payment_schedule_id']);
            $paymentSchedule->paid_amount = ($paymentSchedule->paid_amount ?? 0) + $validated['paid_amount'];
            $paymentSchedule->updateStatus();
            $paymentSchedule->save();

            DB::commit();

            return response()->json([
                'message' => 'Receipt created and status updated successfully',
                'receipt' => $receipt,
                'payment_schedule' => $paymentSchedule
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating receipt: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentSchedule $paymentSchedule)
    {
        //
    }

    // public function recordPayment(Request $request)
    // {
    //     $request->validate([
    //         'schedule_id' => 'required|exists:payment_schedules,id',
    //         'amount' => 'required|numeric|min:0',
    //         'receipt_no' => 'required|string',
    //         'receipt_date' => 'required|date',
    //     ]);

    //     // Find the schedule
    //     $schedule = PaymentSchedule::findOrFail($request->schedule_id);

    //     // Create receipt
    //     $receipt = Receipt::create([
    //         // 'payment_schedule_id' => $schedule->id,
    //         'receipt_no' => $request->receipt_no,
    //         'paid_amount' => $request->amount,
    //         'receipt_date' => Carbon::createFromFormat('d/m/Y', $request->receipt_date),
    //         // Add other receipt fields as needed
    //     ]);

    //     $schedule->paid_amount = ($schedule->paid_amount ?? 0) + $request->amount;

    //     if ($schedule->paid_amount >= $schedule->amount) {
    //         $schedule->status = 'PAID';
    //     } else {
    //         $schedule->status = 'PARTIALLY_PAID';
    //     }

    //     $schedule->save();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Payment recorded successfully',
    //         'receipt' => $receipt,
    //         'schedule' => $schedule->fresh()
    //     ]);
    // }
}
