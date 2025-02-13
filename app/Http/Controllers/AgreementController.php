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
        return Inertia::render('Agreements/Create',[
            'customers' => Customer::all(),
            'agreement_max' => (Agreement::max('agreement_no')??0) + 1,
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
            'agreement_date' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'customer_id' => 'required',
            'currency' => 'required',
        ]);
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
            'agreement' => Agreement::with('customer')->find($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $agreement_no)
    {
        return Inertia::render('Agreements/Create',[
            'customers' => Customer::all(),
            'agreement_max' => (Agreement::max('agreement_no')??0) + 1,
            'csrf_token' => csrf_token(),
            'quotations' => Quotation::all(),
            'agreement' => Agreement::with('paymentSchedules')->find($agreement_no),
            'edit' => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Agreement $agreement)
    {
        $data = $request->except('payment_schedule')+['amount' => $request->agreement_amount];
        $data['attachments'] = json_encode($request->attachments);
        $agreement->update($data);
        //dd($agreement->paymentSchedules->pluck('id'));
        PaymentSchedule::where('agreement_no','=',$agreement->agreement_no)->delete();
        foreach($request->payment_schedule as $key => $value){
            unset($value['id']);
            $schedule = new PaymentSchedule([
                'agreement_no' => $request->agreement_no,
                'due_date' => $value['due_date'],
                'amount' => $value['amount'],
                'status' => $value['status'],
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
     * Upload the specified resource into storage.
     */
    public function upload(Request $request)
    {
        if($request->has('agreement_doc')){
            if($request->has('agreement_doc_old')){
                Storage::disk('public')->delete('agreements/'.basename($request->agreement_doc_old));
            }
            return Storage::url($request->file('agreement_doc')->storePublicly('agreements','public'));
        }else if($request->has('attachments')){
            return Storage::url($request->file('attachments')->storePublicly('attachments','public'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agreement $agreement)
    {
        //
    }
}
