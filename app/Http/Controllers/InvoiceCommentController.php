<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceComment;
use App\Models\InvoiceHdComment;
use App\Models\InvoiceRmComment;
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
            // Step 1: Pre-approval in invoice_hd_comments
            $hdComment = InvoiceHdComment::create([
                'invoice_id' => $invoice->id,
                'role' => 'HD Approval', // Role can be 'HD Approval' or something else as needed
                'status' => 'pending',
                'comment' => $validated['comment'],
            ]);

            // Step 2: Pre-approval in invoice_rm_comments
            $rmComment = InvoiceRmComment::create([
                'invoice_id' => $invoice->id,
                'role' => 'RM Approval', // Role can be 'RM Approval' or something else as needed
                'status' => 'pending',
                'comment' => $validated['comment'],
            ]);

            // Check if both pre-approvals are 'approved'
            if ($hdComment->status === 'approved' && $rmComment->status === 'approved') {
                // Final approval step: Save the final comment in invoice_comments
                InvoiceComment::create([
                    'invoice_id' => $invoice->id,
                    'role' => 'Final Approval', // Role can be 'Final Approval'
                    'status' => 'approved',
                    'comment' => $validated['comment'],
                ]);

                // Now assign final invoice number and date if approved
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

                $invoice->save();
                return response()->json(['message' => 'Invoice approved and final comment saved successfully.']);
            } else {
                // If not all pre-approvals are done, respond with a message
                return response()->json(['message' => 'Invoice requires pre-approval before final approval.'], 400);
            }
        }

        // If status is not approved, just update the status
        $invoice->save();

        // Save the comment if status is pending or rejected
        InvoiceComment::create([
            'invoice_id' => $invoice->id,
            'role' => 'Initial Comment',
            'status' => $validated['status'],
            'comment' => $validated['comment'],
        ]);

        return response()->json(['message' => 'Status and comment saved successfully.']);
    }
}
