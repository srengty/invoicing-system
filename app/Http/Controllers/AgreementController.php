<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\Customer;
use App\Models\PaymentSchedule;
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
        return Inertia::render('Agreements/Index', [
            'agreements' => Agreement::with('customer')->get(),
        ]);
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
        $request->validate([
            'agreement_no' => 'required',
            'agreement_doc' => 'required',
            'agreement_date' => 'required|date_format:d/m/Y',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y',
            'customer_id' => 'required',
            'currency' => 'required',
        ]);
        //dd($request->all());
        $data = $request->except('payment_schedule')+['amount' => $request->agreement_amount];
        $data['attachments'] = json_encode($request->attachments);
        $agreement = Agreement::create($data);
        foreach($request->payment_schedule as $key => $value){
            unset($value['id']);
            $schedule = new PaymentSchedule([
                'agreement_no' => $request->agreement_no,
                'due_date' => $value['due_date'],
                'amount' => $value['amount'],
                'status' => 'Pending',
                'percentage' => $value['percentage'],
                'short_description' => $value['short_description'],
                'currency' => $value['currency'],
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
        return Inertia::render('Agreements/Show', [
            'agreement' => Agreement::with('customer')->with('paymentSchedules')->find($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $agreement_no)
    {
        $agreement = Agreement::with(['customer', 'paymentSchedules', 'quotation'])
            ->findOrFail($agreement_no);

        $customers = Customer::where('active', true)
            ->orWhere('id', $agreement->customer_id)
            ->get();

        return Inertia::render('Agreements/Edit', [
            'agreement' => $agreement,
            'customers' => $customers,
            'quotations' => Quotation::all(),
            // dd("Hello")
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $agreement_no)
    {
        $request->validate([
            'agreement_no' => 'required',
            'agreement_date' => 'required|date_format:d/m/Y',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y',
            'customer_id' => 'required',
            'currency' => 'required',
        ]);

        $agreement = Agreement::with('paymentSchedules')->findOrFail($agreement_no);

        $data = $request->except('payment_schedule', 'attachments') + ['amount' => $request->agreement_amount];

        // Handle attachments
        if ($request->has('attachments')) {
            $data['attachments'] = json_encode($request->attachments);
        }

        $agreement->update($data);

        // Update payment schedules
        $agreement->paymentSchedules()->delete();
        foreach ($request->payment_schedule as $schedule) {
            PaymentSchedule::create([
                'agreement_no' => $request->agreement_no,
                'due_date' => $schedule['due_date'],
                'amount' => $schedule['amount'],
                'status' => $schedule['status'] ?? 'Pending',
                'percentage' => $schedule['percentage'],
                'short_description' => $schedule['short_description'],
                'currency' => $schedule['currency'],
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
        if ($request->has('agreement_doc')) {
            if ($request->has('agreement_doc_old')) {
                Storage::disk('public')->delete('agreements/'.basename($request->agreement_doc_old));
            }

            $file = $request->file('agreement_doc');
            $path = $file->storePublicly('agreements', 'public');

            return response()->json([
                'path' => Storage::url($path),
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);
        }
        else if ($request->has('attachments')) {
            $file = $request->file('attachments');
            $path = $file->storePublicly('attachments', 'public');

            return response()->json([
                'path' => Storage::url($path),
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
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
            return response()->json($quotation->agreement); // Return the agreement data
        }

        return response()->json(null); // Return null if no agreement found
    }
    public function searchQuotation(Request $request)
    {
        $quotationNo = $request->input('quotation_no');
        $quotation = Quotation::where('quotation_no', $quotationNo)
                              ->with('customer') // Ensure the customer is loaded
                              ->first();

        if ($quotation) {
            return response()->json([
                'customer_name' => $quotation->customer->name,
                'address' => $quotation->customer->address,
                'customer_id' => $quotation->customer->id, // Include the customer_id
                'agreement_amount' => $quotation->total, // Replace 'total' with the field name you want
            ]);
        }

        return response()->json(['error' => 'Quotation not found'], 404);
    }



}
