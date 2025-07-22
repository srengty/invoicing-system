<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Agreement;
use App\Models\Invoice;
use App\Models\Receipt;
use Inertia\Inertia;
use App\Models\PaymentSchedule;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetching related data: customers, products, quotations, agreements, invoices, and receipts
        $data = [
            'customers' => Customer::orderBy('created_at', 'desc')->get(),
            'products' => Product::orderBy('created_at', 'desc')->get(),
            'quotations' => Quotation::with(['customer', 'products','comments','latestComment'])->orderBy('created_at', 'desc')->get(),
            'agreements' => Agreement::with(['customer'])->orderBy('created_at', 'desc')->get(),
            'invoices' => Invoice::with(['customer',])->orderBy('created_at', 'desc')->get(),
            'receipts' => Receipt::with(['customer',])->orderBy('created_at', 'desc')->get(),
        ];

        // Get the created_at dates for the respective models to use in charts
        $quotationDates = $data['quotations']->pluck('created_at')->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        });

        $agreementDates = $data['agreements']->pluck('created_at')->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        });

        $invoiceDates = $data['invoices']->pluck('created_at')->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        });

        $receiptDates = $data['receipts']->pluck('created_at')->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        });

        // Calculate the total paid amounts for invoices, receipts, and payment schedules
        $quotationPaidAmount = Quotation::sum('total');
        $invoicePaidAmount = Invoice::sum('paid_amount');
        $receiptPaidAmount = Receipt::sum('paid_amount');
        $paymentSchedulePaidAmount = PaymentSchedule::sum('paid_amount');

        // Calculate the total outstanding amount (grand total minus paid amount)
        $totalOutstanding = $this->getTotalOutstanding();

        // Prepare transactions data to show on the dashboard
        $transactions = [
            ['label' => 'Quotations', 'value' => $quotationPaidAmount],
            ['label' => 'Invoices', 'value' => $invoicePaidAmount],
            ['label' => 'Receipts', 'value' => $receiptPaidAmount],
            ['label' => 'Payment Schedules', 'value' => $paymentSchedulePaidAmount],
        ];

        // Pass all the required data to the Inertia view (Dashboard.vue)
        return Inertia::render('Dashboard', [
            ...$data, // Spread the data array to include customers, products, quotations, etc.
            'transactions' => $transactions, // Pass the transaction summary for display
            'totalOutstanding' => $totalOutstanding, // Pass the total outstanding invoices
            'quotationDates' => $quotationDates, // Pass quotation created_at dates for chart
            'agreementDates' => $agreementDates, // Pass agreement created_at dates for chart
            'invoiceDates' => $invoiceDates, // Pass invoice created_at dates for chart
            'receiptDates' => $receiptDates, // Pass receipt created_at dates for chart
        ]);
    }

    /**
     * Method to calculate the total outstanding invoices
     *
     * @return float Total outstanding invoices
     */
    private function getTotalOutstanding()
    {
        // Calculate total outstanding amount: sum of grand_total minus sum of paid_amount
        return Invoice::sum('grand_total') - Invoice::sum('paid_amount');
    }


    /**
     * Optionally, you can add more methods for fetching aggregated data by month, quarter, etc.
     * Example: Fetch the count of new invoices, agreements, or quotations for each month
     */
    private function getMonthlyData()
    {
        // Example: Fetch count of new invoices by month
        $monthlyInvoices = Invoice::selectRaw('MONTH(created_at) as month, count(*) as total')
            ->groupBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();
        
        return $monthlyInvoices;
    }
}
