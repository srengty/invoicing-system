<?php

namespace App\Http\Controllers;

use App\Models\PaymentSchedule;
use Illuminate\Http\Request;

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
    public function update(Request $request, PaymentSchedule $paymentSchedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentSchedule $paymentSchedule)
    {
        //
    }

    public function recordPayment(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:payment_schedules,id',
            'amount' => 'required|numeric|min:0',
            'receipt_no' => 'required|string',
            'receipt_date' => 'required|date',
        ]);

        // Find the schedule
        $schedule = PaymentSchedule::findOrFail($request->schedule_id);

        // Create receipt
        $receipt = Receipt::create([
            'payment_schedule_id' => $schedule->id,
            'receipt_no' => $request->receipt_no,
            'paid_amount' => $request->amount,
            'receipt_date' => Carbon::createFromFormat('d/m/Y', $request->receipt_date),
            // Add other receipt fields as needed
        ]);

        $schedule->paid_amount = ($schedule->paid_amount ?? 0) + $request->amount;

        if ($schedule->paid_amount >= $schedule->amount) {
            $schedule->status = 'PAID';
        } else {
            $schedule->status = 'PARTIALLY_PAID';
        }

        $schedule->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'receipt' => $receipt,
            'schedule' => $schedule->fresh()
        ]);
    }
}
