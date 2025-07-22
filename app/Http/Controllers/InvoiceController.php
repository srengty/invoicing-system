<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\CustomerCategory;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceComment;
use App\Models\PaymentSchedule;
use App\Models\Receipt;
use App\Models\Category;
use App\Models\InvoiceProduct;
use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;  // Import Carbon for date manipulation
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceEmail;
use App\Models\InvoiceHdComment;
use App\Models\InvoiceRmComment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use Exception;

class InvoiceController extends Controller
{
    // Show the invoice creation page with dropdown options for agreements, quotations, and customers
    public function create()
    {
        // Get all agreements, quotations, customers, and products
        $agreements = Agreement::all();
        $quotations = Quotation::with(['productQuotations', 'agreement', 'productQuotations.product'])
        ->where('status', 'Approved')
        ->get()
        ->map(function ($quotation) {
            $quotation->end_date = null;
            if (!empty($quotation->quotation_date)) {
                // Parse the quotation_date and add 14 days
                $quotationDate = Carbon::parse($quotation->quotation_date);
                $quotation->end_date = $quotationDate->addDays(14)->format('Y-m-d');
            }
            return $quotation;
        });
        $customers = Customer::all();
        $products = Product::all();
        $paymentSchedules = PaymentSchedule::all();
        $receipts = Receipt::all();
        $productCategories = Category::all();
        $invoices = Invoice::select('id', 'quotation_no', 'paid_amount')->get();
        $usedReceiptNos = DB::table('invoice_receipt')->pluck('receipt_no')->toArray();

        // Pass the full models to the form
        return Inertia::render('Invoices/Create', [
            'agreements' => $agreements,
            'quotations' => $quotations,
            'customers' => $customers,
            'products' => $products,
            'paymentSchedules' => $paymentSchedules,
            'receipts'=> $receipts,
            'invoices' => $invoices,
            'productCategories' => Category::all(),
            'usedReceiptNos' => $usedReceiptNos,
        ]);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'invoice_no' => 'nullable|unique:invoices,invoice_no',
            'status' => 'required|string|in:Pending,Approved,Revise',
            'hdStatus' => 'required|string|in:Pending,Approved,Revise',
            'rmStatus' => 'required|string|in:Pending,Approved,Revise',
            'agreement_no' => 'nullable|integer|exists:agreements,agreement_no',
            'quotation_no' => 'nullable|integer|exists:quotations,quotation_no',
            'customer_id' => 'required|exists:customers,id',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'terms' => 'nullable|string|max:255',
            'total_usd' => 'nullable|numeric',
            'exchange_rate' => 'nullable|numeric|min:0',
            'payment_schedules' => 'nullable|array',
            'payment_schedules.*.id' => 'nullable|integer|exists:payment_schedules,id',
            'currency' => 'nullable|string|in:KHR,USD',
            'start_date' => 'nullable|string',
            'end_date' => 'nullable|string',
            'paid_amount' => 'nullable|numeric|min:0',
            'products' => 'nullable|array',
            'products.*.id' => 'required|integer',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.acc_code' => 'nullable|string',
            'products.*.category_id' => 'nullable|integer',
            'products.*.remark' => 'nullable|string',
            'products.*.include_catalog' => 'required|boolean',
            'products.*.pdf_url' => 'nullable|string',
            'receipt_no' => [
                'nullable',
                'integer',
                'exists:receipts,receipt_no',
                function ($attribute, $value, $fail) use ($request) {
                    $used = DB::table('invoice_receipt')->where('receipt_no', $value)->exists();
                    if ($used) {
                        $fail('The selected receipt has already been used for another invoice.');
                    }

                    $receipt = Receipt::where('receipt_no', $value)->first();
                    if ($receipt && $receipt->customer_id != $request->customer_id) {
                        $fail('The selected receipt belongs to a different customer.');
                    }
                }
            ],
        ]);

        if ($validated->fails()) {
            return redirect()->back()
                ->withErrors($validated)
                ->withInput()
                ->with('toast_error', 'Please fix the errors in the form.');
        }

        $data = $validated->validated();

        // Format dates
        $startDate = !empty($data['start_date']) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['start_date'])
            ? Carbon::createFromFormat('d/m/Y', $data['start_date'])->format('Y-m-d')
            : now()->format('Y-m-d');

        $endDate = !empty($data['end_date']) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['end_date'])
            ? Carbon::createFromFormat('d/m/Y', $data['end_date'])->format('Y-m-d')
            : now()->addDays(14)->format('Y-m-d');

        // Calculate grand total from different sources
        $grand_total = 0;

        // 1. From quotation if exists
        if (!empty($data['quotation_no'])) {
            $quotation = Quotation::where('quotation_no', $data['quotation_no'])->first();
            if ($quotation) {
                $grand_total = $quotation->total;
            }
        }
        // 2. From products if no quotation
        elseif (!empty($data['products'])) {
            foreach ($data['products'] as $product) {
                $grand_total += $product['price'] * $product['quantity'];
            }
        }
        // 3. From payment schedules if no products or quotation
        elseif (!empty($data['payment_schedules'])) {
            $paymentSchedules = PaymentSchedule::whereIn('id', collect($data['payment_schedules'])->pluck('id'))->get();
            $grand_total = $paymentSchedules->sum('amount');
        }

        // Handle receipt amounts
        $paid_amount = 0;
        $installment_paid = 0;
        $receipt = null;

        if (!empty($data['receipt_no'])) {
            $receipt = Receipt::where('receipt_no', $data['receipt_no'])->first();

            if ($receipt) {
                $paid_amount = min($receipt->paid_amount, $grand_total);
                $installment_paid = $grand_total - $paid_amount;
            }
        }

        // Calculate USD total if exchange rate provided
        $total_usd = isset($data['exchange_rate']) && $data['exchange_rate'] > 0
            ? $grand_total / $data['exchange_rate']
            : ($data['total_usd'] ?? null);

        // Generate invoice number if approved
        $invoice_no = null;
        if ($data['status'] === 'Approved') {
            $base = (date('Y') - 2025) * 1000000 + 25000001;
            $last = Invoice::where('invoice_no', '>=', $base)->orderBy('invoice_no', 'desc')->first();
            $invoice_no = $last ? $last->invoice_no + 1 : $base;
        }

        // Create the invoice
        $invoice = Invoice::create([
            'invoice_no' => $invoice_no,
            'quotation_no' => $data['quotation_no'] ?? null,
            'agreement_no' => $data['agreement_no'] ?? null,
            'customer_id' => $data['customer_id'],
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'terms' => $data['terms'] ?? null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'grand_total' => $grand_total,
            'total_usd' => $total_usd,
            'exchange_rate' => $data['exchange_rate'] ?? null,
            'currency' => $data['currency'] ?? 'KHR',
            'invoice_date' => $data['status'] === 'Approved' ? now() : null,
            'status' => $data['status'],
            'hdStatus' => $data['hdStatus'],
            'rmStatus' => $data['rmStatus'],
            'paid_amount' => $paid_amount,
            'installment_paid' => $installment_paid,
            'receipt_no' => $data['receipt_no'] ?? null,
        ]);

        // Attach products
        if (!empty($data['products'])) {
            $productsToAttach = [];
            foreach ($data['products'] as $product) {
                $productsToAttach[$product['id']] = [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'include_catalog' => $product['include_catalog'],
                    'pdf_url' => $product['pdf_url'] ?? null,
                ];
            }
            $invoice->products()->sync($productsToAttach);
        }

        // Attach payment schedules pivot
        if (!empty($data['payment_schedules'])) {
            $scheduleIds = collect($data['payment_schedules'])->pluck('id')->toArray();
            $invoice->paymentSchedules()->sync($scheduleIds);
        }

        // Update receipt if used
        if ($receipt) {
            $receipt->update([
                'invoice_no' => $invoice->invoice_no,
                'paid_amount' => $paid_amount,
                'installment_paid' => $installment_paid,
            ]);
        }

        return redirect()->route('invoices.list')->with('success', 'Invoice created successfully!');
    }

    public function generate($id, Request $request)
    {
        $agreements = Agreement::all();
        $quotations = Quotation::with('agreement')->where('status','Approved')->get();
        $customers = Customer::all();
        $paymentSchedules = PaymentSchedule::all();
        $usedReceiptNos = DB::table('invoice_receipt')->pluck('receipt_no')->toArray();
        $receipts = Receipt::whereNotIn('receipt_no', $usedReceiptNos)->get();

        // Modified receipts query to exclude those already used in invoices from payment schedules
        $receipts = Receipt::whereDoesntHave('invoice', function($query) {
            $query->whereHas('paymentSchedules');
        })->get();


        // Get full selected schedule details
        $selectedSchedule = PaymentSchedule::with([
            'agreement' => function($query) {
                $query->with('customer');
            },
            'invoices',
            'receipts'
        ])->find($id);

        $prefill = [
            'payment_schedule_id' => $id,
            'amount' => null,
            'due_date' => null,
            'agreement_no' => null,
        ];

        if ($selectedSchedule) {
            $prefill['amount'] = $selectedSchedule->amount;
            $prefill['due_date'] = $selectedSchedule->due_date;
            $prefill['agreement_no'] = $selectedSchedule->agreement->agreement_no ?? null;
            $prefill['customer_id'] = $selectedSchedule->agreement->customer_id ?? null;
            $prefill['quotation_no'] = $selectedSchedule->agreement->quotation_no ?? null;
            $prefill['phone'] = $selectedSchedule->agreement->customer->phone_number ?? null;
        }

        return Inertia::render('Invoices/Generate', [
            'agreements' => $agreements,
            'quotations' => $quotations,
            'customers' => $customers,
            'paymentSchedules' => $paymentSchedules,
            'receipts' => $receipts,
            'prefill' => $prefill,
            'selectedPaymentSchedule' => $selectedSchedule,
            'usedReceiptNos' => $usedReceiptNos,
        ]);
    }

    public function updateStatusHD(Request $request, Invoice $invoice)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'status' => 'required|string|in:approved,revise,pending',
            'comment' => 'nullable|string|max:1000',
            'role' => 'nullable|string', // optional
        ]);

        // If there is a comment, create an invoice comment for HD
        if (!empty($validated['comment'])) {
            \App\Models\InvoiceHdComment::create([
                'invoice_id' => $invoice->id,
                'status' => $validated['status'],
                'comment' => $validated['comment'],
            ]);
        }

        // Update the invoice with the new HD status
        if ($invoice) {
            $invoice->update([
                'hdStatus' => $validated['status'],  // Update the hdStatus column
            ]);
        }

        // If HD is approved, proceed without dialog for RM
        if ($validated['status'] === 'approved') {
            return redirect()->route('invoices.list')->with('success', 'Invoice HD status updated successfully. RM approval can proceed.');
        }

        return redirect()->route('invoices.list')->with('success', 'Invoice HD status updated successfully.');
    }

    public function updateStatusRM(Request $request, Invoice $invoice)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'status' => 'required|string|in:approved,revise,pending',
            'comment' => 'nullable|string|max:1000',
            'role' => 'nullable|string', // optional
        ]);

        $status = $validated['status'];

        // === If status is 'revise', downgrade RM and HD ===
        if ($status === 'revise') {
            $invoice->rmStatus = 'revise';
            $invoice->hdStatus = 'revise';

            // Save revise comment
            \App\Models\InvoiceRmComment::create([
                'invoice_id' => $invoice->id,
                'status' => 'revise',
                'comment' => $validated['comment'] ?? '',
            ]);

            $invoice->save();

            return redirect()->route('invoices.list')->with('success', 'Invoice set for revision.');
        }

        // === For 'approved' or 'pending' ===

        // Check if HD is approved before allowing RM approval
        $hdComment = \App\Models\InvoiceHdComment::where('invoice_id', $invoice->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($status === 'approved' && (!$hdComment || $hdComment->status !== 'approved')) {
            return redirect()->route('invoices.list')->with('error', 'Cannot update RM status. HD status must be approved first.');
        }

        // Save RM comment if provided
        if (!empty($validated['comment'])) {
            \App\Models\InvoiceRmComment::create([
                'invoice_id' => $invoice->id,
                'status' => $status,
                'comment' => $validated['comment'],
            ]);
        }

        // Update RM status on the invoice
        $invoice->rmStatus = $status;
        $invoice->save();

        return redirect()->route('invoices.list')->with('success', 'Invoice RM status updated successfully.');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'status' => 'required|string|in:approved,revise,pending',
            'comment' => 'nullable|string|max:1000',
            'role' => 'nullable|string', // optional
        ]);

        // Update the status of the invoice
        $invoice->status = $validated['status'];

        $status = $validated['status'];

        if ($invoice->rmStatus === 'revise') {
            $invoice->rmStatus = 'revise';
            $invoice->hdStatus = 'revise';

            // Save revision comment
            \App\Models\InvoiceComment::create([
                'invoice_id' => $invoice->id,
                'rmStatus' => 'revise',
                'comment' => $validated['comment'] ?? '',
            ]);

            $invoice->save();


        } elseif ($status === 'revise') {
            // Downgrade all: RM, HD, and main status
            $invoice->rmStatus = 'revise';
            $invoice->hdStatus = 'revise';
            $invoice->status = 'revise';

            // Save revision comment
            \App\Models\InvoiceComment::create([
                'invoice_id' => $invoice->id,
                'status' => 'revise',
                'comment' => $validated['comment'] ?? '',
            ]);

            $invoice->save();
        }

        // Check if the invoice status is 'approved'
        if ($invoice->status === 'approved') {

            // Generate the invoice number if it doesn't exist
            if (!$invoice->invoice_no) {
                $year = date('Y');
                $baseInvoiceNo = ($year - 2025) * 1000000 + 25000001;
                $lastInvoice = Invoice::where('invoice_no', '>=', $baseInvoiceNo)
                    ->orderBy('invoice_no', 'desc')
                    ->first();
                $invoice->invoice_no = $lastInvoice ? $lastInvoice->invoice_no + 1 : $baseInvoiceNo;
            }

            // Set the invoice date to the current date
            $invoice->invoice_date = now();

            // Automatically set the invoice_end_date to 14 days after the invoice_date
            $invoice->invoice_end_date = Carbon::parse($invoice->invoice_date)->addDays(14)->format('Y-m-d'); // 14 days after invoice_date

            // Set the customer status to 'Sent'
            $invoice->customer_status = 'Sent';
        }

        // Check if RM status is 'approved' before making the last step
        if ($invoice->status === 'approved') {
            // Get the latest InvoiceRmComment to check its status
            $rmComment = \App\Models\InvoiceRmComment::where('invoice_id', $invoice->id)
                ->orderBy('created_at', 'desc') // Get the latest comment
                ->first();

            if ($rmComment && $rmComment->status === 'approved') {
                // Only proceed with the invoice update if RM status is approved
                // Save the updated invoice to the database
                $invoice->save();

                // If there is a related receipt, update the receipt with the new invoice number
                if ($invoice->receipt_no && $invoice->invoice_no) {
                    Receipt::where('receipt_no', $invoice->receipt_no)
                        ->update(['invoice_no' => $invoice->invoice_no]);
                }

                // If there is a comment, create a generic invoice comment
                if (!empty($validated['comment'])) {
                    \App\Models\InvoiceComment::create([
                        'invoice_id' => $invoice->id,
                        'status' => $validated['status'],
                        'comment' => $validated['comment'],
                    ]);
                }

                return redirect()->route('invoices.index')->with('success', 'Invoice status updated successfully.');
            } else {
                // If RM status is not 'approved', return an error message
                return redirect()->route('invoices.index')->with('error', 'Cannot update invoice status. RM status must be approved first.');
            }
        }

        // If the status isn't approved, just save without making any additional changes
        $invoice->save();

        return redirect()->route('invoices.index')->with('success', 'Invoice status updated successfully.');
    }

    // Show the list of invoices
    public function index(Request $request)
    {
        // Initialize the query builder for the invoices
        $query = Invoice::with('customer', 'agreement', 'quotation', 'products', 'customer_category', 'invoiceComments') ->where('status', 'Approved');;

        // Apply pagination after all filters
        $invoices = $query->orderBy('created_at', 'desc')->paginate();

        // Get the filters for passing them to the view
        $filters = $request->only(['invoice_no_start', 'invoice_no_end', 'category_name_english', 'currency', 'status', 'start_date', 'end_date', 'customer']);

        // Return the filtered invoices to the view
        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices,
            'filters' => $filters,
        ]);
    }

    public function list(Request $request)
    {
        // Initialize the query builder for the invoices
        $query = Invoice::with('customer', 'agreement', 'quotation', 'products', 'customer_category', 'invoiceComments', 'paymentSchedules', 'hdComments', 'rmComments');

        // Apply pagination after all filters
        $invoices = $query->orderBy('created_at', 'desc')->paginate();
        $hdComments = InvoiceHdComment::orderBy('created_at')->get();
        $rmComments = InvoiceRmComment::orderBy('created_at')->get();

        // Get the filters for passing them to the view
        $filters = $request->only(['invoice_no_start', 'invoice_no_end', 'category_name_english', 'currency', 'status', 'start_date', 'end_date', 'customer']);
            //   $response=Http::withoutVerifying()->post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
            //         'chat_id' =>"1166075692",
            //         'text' => "Hi , your Telegram is now linked.",
            //     ]);

        // Return the filtered invoices to the view
        return Inertia::render('Invoices/List', [
            'invoices'             => $invoices,
            'filters'              => $filters,
            'hdComments'           => $hdComments,
            'rmComments'           => $rmComments,
            'hdPendingCount'       => Invoice::whereIn('hdStatus', ['Pending','Revise'])->count(),
            'rmPendingCount'       => Invoice::whereIn('rmStatus', ['Pending','Revise'])->count(),
            'dirPendingCount'      => Invoice::whereIn('status',   ['Pending','Revise'])->count(),
        ]);
    }

    public function show($invoice_no)
    {
        // Load the invoice with customer
        $invoice = Invoice::with(['customer','paymentSchedules'])->where('id', $invoice_no)->firstOrFail();

        if ($invoice->status === 'Approved') {
            abort(403, 'This invoice has been paid and can no longer be modified.');
        }

        $products = $invoice->products()->get();

        // Get products manually via invoice_product
        $products = DB::table('invoice_product')
        ->join('products', 'invoice_product.product_id', '=', 'products.id')
        ->where('invoice_product.invoice_id', $invoice->id)
        ->select(
            'products.id as product_id',
            'products.name as product_name',
            'products.name_kh as product_name_kh',
            'products.code as product_code',
            'products.desc',
            'products.desc_kh',
            'invoice_product.quantity',
            'invoice_product.price',
            'invoice_product.include_catalog'
        )
        ->get();

        $paymentSchedules = $invoice->paymentSchedules->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'amount' => $schedule->amount,
                'short_description' => $schedule->short_description,
                // add more fields if needed
            ];
        });

        return Inertia::render('Invoices/Print', [
            'invoice' => [
                'id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
                'invoice_date' => $invoice->invoice_date
                    ? Carbon::parse($invoice->invoice_date)->format('Y-m-d')
                    : null,
                'due_date' => $invoice->due_date
                    ? Carbon::parse($invoice->due_date)->format('Y-m-d')
                    : null,
                'customer_id' => $invoice->customer_id,
                'customer_name' => $invoice->customer->name,
                'customer_name_kh' => $invoice->customer->name_kh ?? $invoice->customer->name,
                'address' => $invoice->address ?? $invoice->customer->address,
                'phone_number' => $invoice->phone_number ?? $invoice->customer->phone_number,
                'email' => $invoice->email ?? $invoice->customer->email,
                'telegram_chat_id' => $invoice->telegram_chat_id ?? $invoice->customer->telegram_chat_id,
                'products' => $products,
                'grand_total' => $invoice->grand_total,
                'total_usd' => $invoice->total_usd,
                'exchange_rate' => $invoice->exchange_rate,
                'terms' => $invoice->terms ?? 'Standard payment terms apply',
                'status' => $invoice->status,
                'payment_status' => $invoice->payment_status,
                'authorized_by' => $invoice->authorized_by,
                'accepted_by' => $invoice->accepted_by,
                'quotation_no' => $invoice->quotation_no,
                'agreement_no' => $invoice->agreement_no,
                'acceptance_date' => $invoice->acceptance_date
                    ? Carbon::parse($invoice->acceptance_date)->format('Y-m-d')
                    : null,
                'payment_schedules' => $paymentSchedules,
            ],
        ]);
    }

    public function edit($id)
    {
        $invoice = Invoice::with(['customer', 'products', 'receipts'])->findOrFail($id);

        return Inertia::render('Invoices/Edit', [
            'invoice' => $invoice,
            'agreements' => Agreement::all(),
            'quotations' => Quotation::all(),
            'customers' => Customer::all(),
            'products' => Product::all(),
            'receipts' => Receipt::all(),
        ]);
    }

    // Show the edit page for an existing invoice
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'agreement_no' => 'nullable|integer|exists:agreements,agreement_no',
            'quotation_no' => 'nullable|integer|exists:quotations,quotation_no',
            'customer_id' => 'required|exists:customers,id',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'terms' => 'nullable|string|max:255',
            'total_usd' => 'nullable|numeric',
            'exchange_rate' => 'nullable|numeric|min:0',
            'payment_schedules' => 'nullable|array',
            'payment_schedules.*.id' => 'nullable|integer|exists:payment_schedules,id',
            'currency' => 'nullable|string|in:KHR,USD',
            'start_date' => 'nullable|string',
            'end_date' => 'nullable|string',
            'paid_amount' => 'nullable|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.include_catalog' => 'nullable|boolean',
            'products.*.pdf_url' => 'nullable|string',
            'receipt_no' => 'nullable|integer',
        ]);

        try {
            $invoice = Invoice::findOrFail($id);

            $startDate = $this->parseDate($data['start_date'] ?? null, now());
            $endDate = $this->parseDate($data['end_date'] ?? null, now()->addDays(14));

            $grand_total = 0;

            // Calculate grand total
            if (!empty($data['payment_schedules'])) {
                $grand_total = collect($data['payment_schedules'])->sum(fn ($s) =>
                    PaymentSchedule::find($s['id'])?->amount ?? 0
                );
            } elseif (!empty($data['quotation_no'])) {
                $quotation = Quotation::where('quotation_no', $data['quotation_no'])->first();
                $grand_total = $quotation?->total ?? 0;
            } else {
                foreach ($data['products'] as $product) {
                    $productData = Product::find($product['id']);
                    if ($productData && $productData->status === 'approved') {
                        $grand_total += $product['price'] * $product['quantity'];
                    }
                }
            }

            // Receipt handling
            $receipt = !empty($data['receipt_no'])
                ? Receipt::where('receipt_no', $data['receipt_no'])->first()
                : null;

            $paid_amount = 0;
            $installment_paid = 0;

            if ($receipt) {
                $paid_amount = $receipt->paid_amount;

                if ($receipt->installment_paid > 0) {
                    $paid_amount = min($receipt->installment_paid, $grand_total);
                    $installment_paid = max($receipt->installment_paid - $paid_amount, 0);
                } elseif ($paid_amount > $grand_total) {
                    $installment_paid = $paid_amount - $grand_total;
                    $paid_amount = $grand_total;
                }
            }

            if (empty($data['receipt_no'])) {
                $paid_amount = 0;
                $installment_paid = 0;
            }

            // USD calculation
            $exchangeRate = $data['exchange_rate'] ?? null;
            $total_usd = $exchangeRate && $exchangeRate > 0
                ? round($grand_total / $exchangeRate, 2)
                : ($data['total_usd'] ?? null);

            // === Normalize statuses ===
            $invoice->status = $invoice->status === 'revise' ? 'Update' : 'Pending';
            $invoice->rmStatus = $invoice->rmStatus === 'revise' ? 'Update' : 'Pending';
            $invoice->hdStatus = $invoice->hdStatus === 'revise' ? 'Update' : 'Pending';

            // Update invoice
            $invoice->update([
                'quotation_no' => $data['quotation_no'] ?? null,
                'agreement_no' => $data['agreement_no'] ?? null,
                'customer_id' => $data['customer_id'],
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'terms' => $data['terms'] ?? null,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'grand_total' => $grand_total,
                'total_usd' => $total_usd,
                'exchange_rate' => $exchangeRate,
                'currency' => $data['currency'] ?? 'KHR',
                'status' => $invoice->status,         // normalized
                'rmStatus' => $invoice->rmStatus,     // normalized
                'hdStatus' => $invoice->hdStatus,     // normalized
                'paid_amount' => $paid_amount,
                'installment_paid' => $installment_paid,
                'receipt_no' => $data['receipt_no'] ?? null,
            ]);

            // Sync products
            $pivotData = [];
            foreach ($data['products'] as $product) {
                $pivotData[$product['id']] = [
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'include_catalog' => $product['include_catalog'] ?? false,
                    'pdf_url' => $product['pdf_url'] ?? null,
                ];
            }
            $invoice->products()->sync($pivotData);

            // Sync payment schedules
            $invoice->paymentSchedules()->detach();
            if (!empty($data['payment_schedules'])) {
                $ids = collect($data['payment_schedules'])->pluck('id')->toArray();
                $invoice->paymentSchedules()->attach($ids);

                if ($receipt) {
                    PaymentSchedule::whereIn('id', $ids)->update(['status' => 'Paid']);
                }
            }

            // Update receipt
            if ($receipt && $invoice->invoice_no) {
                $receipt->update([
                    'invoice_no' => $invoice->invoice_no,
                    'installment_paid' => $installment_paid,
                ]);
            }

            return redirect()->route('invoices.list')->with('success', 'Invoice updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    private function parseDate(?string $date, Carbon $fallback): string
    {
        return $date && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)
            ? Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d')
            : $fallback->format('Y-m-d');
    }

    // Delete an invoice
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Detach any products related to this invoice before deletion
        $invoice->products()->detach();

        // Delete the invoice
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully!');
    }

    public function getInvoicesByQuotation($quotation_no)
    {
        // Fetch invoices for the given quotation_no and status=paid
        $invoices = Invoice::where('quotation_no', $quotation_no)
                            ->where('status', 'paid')
                            ->get();

        // Return the invoices as a JSON response
        return response()->json($invoices);
    }

    public function updateCustomerStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_status' => 'required|in:Sent,Pending,Accept,Reject',
            'comment' => 'nullable|string',
        ]);

        $quotation = Quotation::findOrFail($id);

        $quotation->update([
            'customer_status' => $validated['customer_status'],
        ]);

        if ($request->has('comment')) {
            // Save comment logic here
            $quotation->comments()->create([
                'comment' => $validated['comment'],
                // 'role' => auth()->user()->role, // or get role from request
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function sendInvoice(Request $request)
    {

        // Find the invoice by its ID
        $invoice = Invoice::with('customer')->find($request->input('invoice_id'));
        
        // try {
        //     // Email validation (only if sending email)
        //     if ($request->input('send_email')) {
        //         $customerEmail = $invoice->customer->email;
        //         if (!$customerEmail) {
        //             throw new Exception('Customer email not found.');
        //         }
        //     }

        //     // PDF handling
        //     if ($request->hasFile('pdf_file')) {
        //         // Save the PDF file in the public storage
        //         $pdfPath = $request->file('pdf_file')->store('invoices', 'public');
        //         Log::info('Invoice PDF saved to: ' . $pdfPath);
        //     } else {
        //         throw new Exception('Invoice PDF file not found.');
        //     }

        //     // Email sending
        //     if ($request->input('send_email')) {
        //         Log::info('Sending email to: ' . $customerEmail);
        //         Mail::to($customerEmail)->send(new InvoiceEmail($invoice, $request->file('pdf_file')));

        //         // Automatically update statuses when sending email
        //         $invoice->customer_status = 'Pending'; // Change the invoice status to 'Sent'
        //         $invoice->customer->update(['customer_status' => 'Pending']); // Update customer status to Pending
        //     }

        //     // Save the invoice (status remains unless changed elsewhere)
        //     $invoice->save();

        //     return  redirect()->route('invoices.list')->with('success', 'Invoice email sent successfully!');
        // } catch (Exception $e) {
        //     Log::error('Failed to send invoice: ' . $e->getMessage());
        //     return;
        // }

            try {
            // Validate email only if sending email
            if ($request->input('send_email')) {
                $customerEmail = $invoice->customer->email;
                if (!$customerEmail) {
                    throw new \Exception('Customer email not found.');
                }
            }

            // Validate & store the PDF file
            if ($request->hasFile('pdf_file')) {
                $pdfPath = $request->file('pdf_file')->store('invoices', 'public');
                Log::info('Invoice PDF saved to: ' . $pdfPath);
            } else {
                // throw new \Exception('Invoice PDF file not found.');
            }

            // === Send Email ===
            if ($request->input('send_email')) {
                Log::info('Sending email to: ' . $customerEmail);
                Mail::to($customerEmail)->send(new InvoiceEmail($invoice, $request->file('pdf_file')));

                // Update status
                $invoice->customer_status = 'Pending';
                $invoice->customer->update(['customer_status' => 'Pending']);
            }
            // === Send Telegram ===
            if ($request->input('send_telegram')) {
                $chatId = $invoice->customer->telegram_chat_id;

                if (!$chatId) {
                    Log::warning('Customer does not have a Telegram chat ID.');
                    throw new \Exception('Customer has no Telegram chat ID.');
                }

                $message = "📄 Invoice #{$invoice->invoice_no} is ready.\n"
                        . "Customer: {$invoice->customer->name}\n"
                        . "Total: " . number_format($invoice->grand_total, 2) . "៛";
                        // dd($chatId,env('TELEGRAM_BOT_TOKEN'));
                        
                $response = Http::withoutVerifying()->post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML'
                ]);

                if (!$response->successful()) {
                    Log::error('Telegram API Error: ' . $response->body());
                    throw new \Exception('Telegram message could not be sent.');
                }

                Log::info("Telegram message sent to chat ID: {$chatId}");

                // Update status if not already updated
                $invoice->customer_status = 'Pending';
                $invoice->customer->update(['customer_status' => 'Pending']);
            }

            // Save invoice status updates
            $invoice->save();

            return redirect()->route('invoices.list')->with('success', 'Invoice sent successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to send invoice: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Send failed: ' . $e->getMessage());
        }

        // try {
        //     $sendMethods = $request->input('send_methods', []);
        //     $results = [
        //         'email' => false,
        //         'telegram' => false
        //     ];

        //     // Store PDF if email is selected
        //     $pdfPath = null;
            
        //     if (in_array('email', $sendMethods)) {
        //         // Email validation
        //         $customerEmail = $invoice->customer->email;
        //         dd('hi1');
        //         if (!$customerEmail) {
        //             throw new Exception('Customer email not found.');
        //         }

        //         // PDF handling
        //         if ($request->hasFile('pdf_file')) {
        //             // Save the PDF file in the public storage
        //             $pdfPath = $request->file('pdf_file')->store('invoices', 'public');
        //             Log::info('Invoice PDF saved to: ' . $pdfPath);
        //         } else {
        //             throw new Exception('Invoice PDF file not found.');
        //         }
        //     }

        //     // Handle email sending
        //     if (in_array('email', $sendMethods)) {
        //         $pdfFile = $request->file('pdf_file');
        //         $customerEmail = $invoice->customer->email;
                
        //         Log::info('Sending email to: ' . $customerEmail);
        //         Mail::to($customerEmail)->send(new InvoiceEmail($invoice, $pdfFile));
        //         $results['email'] = true;
                
        //         Log::info("Invoice email sent to: {$customerEmail}");
        //     }

        //     // Handle Telegram sending (remove the dd('hi3') debug line)
        //     if (in_array('telegram', $sendMethods)) {
        //         if ($invoice->customer->telegram_chat_id) {
        //             $message = $this->generateTelegramMessage($invoice);
                    
        //             $response = Http::post("https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage", [
        //                 'chat_id' => $invoice->customer->telegram_chat_id,
        //                 'text' => $message,
        //                 'parse_mode' => 'HTML'
        //             ]);

        //             if ($response->successful()) {
        //                 $results['telegram'] = true;
        //                 Log::info('Telegram message sent to customer ID: '.$invoice->customer->id);
        //             } else {
        //                 Log::error('Telegram API error: '.$response->body());
        //                 throw new Exception('Failed to send Telegram message');
        //             }
        //         } else {
        //             Log::warning('Customer has no Telegram chat ID: '.$invoice->customer->id);
        //             throw new Exception('Customer has no Telegram chat ID');
        //         }
        //     }

        //     // Update status if any method succeeded
        //     if ($results['email'] || $results['telegram']) {
        //         $invoice->customer_status = 'Pending';
        //         $invoice->customer->customer_status = 'Pending';
        //         $invoice->push(); // Save both invoice and customer
        //     }

        //     // Build success message
        //     $messages = [];
        //     if ($results['email']) $messages[] = 'Email sent successfully';
        //     if ($results['telegram']) $messages[] = 'Telegram notification sent';
            
        //     return redirect()
        //         ->route('invoices.list')
        //         ->with('success', implode(' and ', $messages));
        // } catch (Exception $e) {
        //     Log::error('Invoice sending failed: '.$e->getMessage());
        //     return redirect()
        //         ->back()
        //         ->withInput()
        //         ->with('error', 'Failed to send invoice: '.$e->getMessage());
        // }
    }

    private function generateTelegramMessage(Invoice $invoice): string
    {
        $message = "<b>📄 New Invoice Notification</b>\n\n";
        $message .= "Invoice #: <b>{$invoice->invoice_no}</b>\n";
        $message .= "Amount: <b>{$invoice->grand_total} {$invoice->currency}</b>\n";
        $message .= "Due Date: <b>" . Carbon::parse($invoice->end_date)->format('M d, Y') . "</b>\n\n";
        $message .= "Please check your email for the detailed invoice or contact us for any questions.\n\n";
        $message .= "Thank you for your business!";
        
        return $message;
    }

    public function updatePaymentStatus($invoiceId, Request $request)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        $validated = $request->validate([
            'payment_status' => 'required|in:Fully Paid,Partially Paid,Pending,Overdue,Cancelled',
        ]);

        $invoice->payment_status = $validated['payment_status'];
        $invoice->save();

        return response()->json($invoice);
    }

    public function sendTelegramToMultiple(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'integer|exists:customers,id',
        ]);

        $botToken = env('TELEGRAM_BOT_TOKEN');
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        $customers = Customer::whereIn('id', $validated['customer_ids'])
            ->whereNotNull('telegram_chat_id')
            ->get();

        $results = [];

        foreach ($customers as $customer) {
            try {
                $response = Http::post($url, [
                    'chat_id' => $customer->telegram_chat_id,
                    'text' => $validated['message'],
                ]);

                $results[] = [
                    'customer_id' => $customer->id,
                    'chat_id' => $customer->telegram_chat_id,
                    'success' => $response->successful(),
                    'response' => $response->json(),
                ];
            } catch (\Exception $e) {
                Log::error('Telegram message send failed', [
                    'customer_id' => $customer->id,
                    'chat_id' => $customer->telegram_chat_id,
                    'error' => $e->getMessage(),
                ]);

                $results[] = [
                    'customer_id' => $customer->id,
                    'chat_id' => $customer->telegram_chat_id,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'summary' => [
                'total' => count($customers),
                'sent' => count(array_filter($results, fn ($r) => $r['success'])),
            ],
            'details' => $results,
        ]);
    }

}
