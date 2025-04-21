<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceComment;
use Illuminate\Http\Request;

class InvoiceCommentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:approved,rejected,pending',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Update invoice status
        $invoice->status = $validated['status'];

        if ($validated['status'] === 'approved') {
            if (!$invoice->invoice_no) {
                $year = date('Y');
                $base = ($year - 2025) * 1000000 + 25000001;
                $last = Invoice::where('invoice_no', '>=', $base)
                    ->orderBy('invoice_no', 'desc')->first();
                $invoice->invoice_no = $last ? $last->invoice_no + 1 : $base;
            }

            if (!$invoice->invoice_date) {
                $invoice->invoice_date = now();
            }
        }

        $invoice->save();

        // Save the comment
        InvoiceComment::create([
            'invoice_no' => $invoice->id,
            'status' => $validated['status'],
            'comment' => $validated['comment'],
        ]);

        return response()->json(['message' => 'Status and comment saved successfully.']);
    }
}

