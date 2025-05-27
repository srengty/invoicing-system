<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Agreement;
use App\Models\Invoice;
use App\Models\Receipt;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $data = [
                'customers' => Customer::select(['id', 'code', 'name', 'credit_period', 'customer_category_id', 'created_at'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),

                'items' => Product::select(['id', 'code', 'name', 'unit', 'price', 'created_at'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),

                'quotations' => Quotation::with(['customer:id,name'])
                    ->select(['id', 'quotation_no', 'customer_id', 'total as amount', 'status', 'created_at'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),

                'agreements' => Agreement::with(['customer:id,name'])
                    ->select(['agreement_no', 'customer_id', 'amount', 'status', 'created_at'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),

                'invoices' => Invoice::with(['customer:id,name'])
                    ->select(['id', 'invoice_no', 'customer_id', 'grand_total as amount', 'status', 'created_at'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),

                'receipts' => Receipt::with(['customer:id,name'])
                    ->select(['receipt_no', 'customer_id', 'paid_amount', 'payment_method', 'created_at'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),
            ];

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load dashboard data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
