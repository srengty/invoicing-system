<?php
use App\Http\Controllers\AgreementController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\CustomerCategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceCommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\PaymentScheduleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductCommentController;
use App\Http\Controllers\QuotationEmailController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Application;
use App\Mail\TestMail;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canRegister'   => Route::has('register'),
        'laravelVersion'=> Application::VERSION,
        'phpVersion'    => PHP_VERSION,
    ]);
});

// Authentication (Login / Logout)
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
// Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard Routes (Role-based)
    |--------------------------------------------------------------------------
    */
    Route::middleware([CheckRole::class.':director'])
        ->get('/dashboard/  ', function () {
            return Inertia::render('Dashboard', [
                'userRoles' => request()->session()->get('roles', []),
            ]);
        })->name('dashboard.director');

    Route::middleware([CheckRole::class.':division staff'])
        ->get('/dashboard/division-staff', function () {
            return Inertia::render('Dashboard', [
                'userRoles' => request()->session()->get('roles', []),
            ]);
        })->name('dashboard.division-staff');

    Route::middleware([CheckRole::class.':chef department'])
        ->get('/dashboard/chef-department', function () {
            return Inertia::render('Dashboard', [
                'userRoles' => request()->session()->get('roles', []),
            ]);
        })->name('dashboard.chef-department');

    Route::middleware([CheckRole::class.':revenue manager'])
        ->get('/dashboard/revenue-manager', function () {
            return Inertia::render('Dashboard', [
                'userRoles' => request()->session()->get('roles', []),
            ]);
        })->name('dashboard.revenue-manager');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        /*
    |--------------------------------------------------------------------------
    | Agreement Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/agreements', [AgreementController::class, 'index'])->name('agreements.index');
    // Route::get('/agreements/create', [AgreementController::class, 'create'])->name('agreements.create');
    Route::get('/agreements/{agreement_no}', [AgreementController::class, 'show'])->name('agreements.show');
    Route::post('/agreements/store', [AgreementController::class, 'store'])->name('agreements.store');
    Route::post('/agreements/upload', [AgreementController::class, 'upload'])->name('agreements.upload');
    Route::put('/agreements/{agreement_no}', [AgreementController::class, 'update'])->name('agreements.update');
    Route::get('/agreements/{agreement_no}/edit', [AgreementController::class, 'edit'])->name('agreements.edit');
    Route::get('/invoices/generate/{id}', [InvoiceController::class, 'generate'])->name('invoices.generate');
    Route::put('/agreements/{agreement_no}', [AgreementController::class, 'update'])->name('agreements.update');
    Route::get('/quotations/{quotationId}/agreement', [QuotationController::class, 'getAgreementForQuotation']);
    Route::post('/api/invoices/filter', [InvoiceController::class, 'filter']);
    Route::get('/search-quotation', [AgreementController::class, 'searchQuotation']);
    Route::get('/check-agreement-reference', [AgreementController::class, 'checkDuplicateReference']);

    /*
    |--------------------------------------------------------------------------
    | Quotation Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('quotations', QuotationController::class)->except(['create','store']);
    Route::get('/quotations', [QuotationController::class, 'list']);
    Route::get('/quotations', [QuotationController::class, 'list'])->name('quotations.list');
    // Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
    // Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
    Route::put('/quotations/{id}/update-status', [QuotationController::class, 'updateStatus']);
    Route::post('/quotations/{quotationId}/comments', [QuotationController::class, 'storeComment']);
    Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
    Route::put('/quotations/{id}/toggle-active', [QuotationController::class, 'toggleActive']);
    Route::put('/quotations/{id}/mark-printed', [QuotationController::class, 'markPrinted']);
    Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::get('/quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print')->middleware('auth');
    Route::get('/quotations/{quotation_no}/pdf', [QuotationController::class, 'generatePDF'])->name('quotations.pdf');

    /*
    |--------------------------------------------------------------------------
    | Invoice Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/invoices/list', [InvoiceController::class, 'list'])->name('invoices.list');
    Route::resource('invoices', InvoiceController::class)->except(['create']);
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    // Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::get('/quotations/{quotation_no}/invoices', [InvoiceController::class, 'getInvoicesByQuotation']);
    Route::put('/invoices/{invoice}/update-status', [InvoiceController::class, 'updateStatus'])->name('invoices.updateStatus');
    Route::put('/invoices/{invoice}/update-status-hd', [InvoiceController::class, 'updateStatusHD'])->name('invoices.updateStatusHD');
    Route::put('/invoices/{invoice}/update-status-rm', [InvoiceController::class, 'updateStatusRM'])->name('invoices.updateStatusRM');
    Route::post('/invoices/send',[InvoiceController::class, 'sendInvoice'])->name('invoices.send');

    /*
    |--------------------------------------------------------------------------
    | Receipt Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('receipts', ReceiptController::class);
    Route::get('receipts/{id}/print', [ReceiptController::class, 'print'])->name('receipts.print');

    /*
    |--------------------------------------------------------------------------
    | Payment Schedule Routes
    |--------------------------------------------------------------------------
    */
    Route::post('/payment-schedules/{id}/receipt', [PaymentScheduleController::class, 'createReceipt'])->name('payment-schedules.createReceipt');
    Route::put('/payment-schedules/{id}', [PaymentScheduleController::class, 'update'])->name('payment-schedules.update');
    Route::put('/payment-schedules/{invoice_no}/update-status', [PaymentScheduleController::class, 'updateStatus']);

    /*
    |--------------------------------------------------------------------------
    | Customer Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/settings/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/api/customers', [CustomerController::class, 'apiIndex']);
    Route::get('/settings/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/settings/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/settings/customers/show/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::put('/settings/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::get('/settings/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::delete('/settings/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::put('/settings/customers/{customer}/toggle-active', [CustomerController::class, 'toggleActive'])->name('customers.toggleActive');
    Route::get('/activate-all-customers', function() {
        \App\Models\Customer::query()->update(['active' => true]);
        return 'All customers activated';
    });
    /*
    |--------------------------------------------------------------------------
    | Product Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/pdfs/{filename}', [ProductController::class, 'viewPdf'])->where('filename', '.*')->name('pdf.view');
    Route::post('/settings/products', [ProductController::class, 'getDepartments'])->name('products.index');
    Route::put('/settings/products/{product}/toggleStatus', [ProductCommentController::class, 'store']);
    Route::put('/settings//products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggleStatus');
    Route::resource('/settings/products', ProductController::class);
    Route::get('/settings', [CustomerController::class, 'index'])->name('settings');

    /*
    |--------------------------------------------------------------------------
    | Category Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/settings/customer-categories', [CustomerCategoryController::class, 'index'])->name('customerCategory.index');
    Route::get('/settings/product-categories', [ProductCategoryController::class, 'index'])->name('productCategory.index');

    // routes/api.php
    /*
    |--------------------------------------------------------------------------
    | Email Routes
    |--------------------------------------------------------------------------
    */
    Route::post('/quotations/send',[QuotationController::class, 'sendQuotation'])->name('quotations.send');
    Route::get('/send-quotation-email', [QuotationEmailController::class, 'sendEmail']);

});
    /*
    |--------------------------------------------------------------------------
    | Role-Specific Routes
    |--------------------------------------------------------------------------
    */
    // Division Staff Routes
    Route::middleware([ CheckRole::class . ':division staff' ])->group(function () {
        // Quotations
        Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
        Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
        // Invoices
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    });
    // Chef Department Routes
    Route::middleware([ CheckRole::class . ':chef department' ])->group(function () {
        // Agreements
        Route::get('/agreements/create', [AgreementController::class, 'create'])->name('agreements.create');
    });

    Route::middleware(['auth'])
        ->get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })
        ->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

require __DIR__.'/auth.php';
