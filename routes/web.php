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
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Application;
use App\Mail\TestMail;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canRegister'   => Route::has('register'),
        'laravelVersion'=> Application::VERSION,
        'phpVersion'    => PHP_VERSION,
    ]);
});

// Authentication (Login / Logout)
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
     ->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
     ->name('logout');

// 1) Dashboard for Directors only
Route::middleware(['auth', 'check.role:Director'])
     ->get('/dashboard/director', function () {
         return Inertia::render('Dashboard', [
             'userRoles' => request()->session()->get('roles', []),
         ]);
     })
     ->name('dashboard.director');

// 2) Dashboard for Division Staff only
Route::middleware(['auth', 'check.role:Division Staff'])
     ->get('/dashboard/division-staff', function () {
         return Inertia::render('Dashboard', [
             'userRoles' => request()->session()->get('roles', []),
         ]);
     })
     ->name('dashboard.division-staff');

// 3) Dashboard for Chef Department only
Route::middleware(['auth', 'check.role:Chef Department'])
     ->get('/dashboard/chef-department', function () {
         return Inertia::render('Dashboard', [
             'userRoles' => request()->session()->get('roles', []),
         ]);
     })
     ->name('dashboard.chef-department');

// 4) Dashboard for Revenue Manager only
Route::middleware(['auth', 'check.role:Revenue Manager'])
     ->get('/dashboard/revenue-manager', function () {
         return Inertia::render('Dashboard', [
             'userRoles' => request()->session()->get('roles', []),
         ]);
     })
     ->name('dashboard.revenue-manager');

// 5) Generic Dashboard (any authenticated & verified user lands here by default)
Route::middleware(['auth', 'verified'])
     ->get('/dashboard', function () {
         return Inertia::render('Dashboard');
     })
     ->name('dashboard');


// Agreements
Route::get('/agreements', [AgreementController::class, 'index'])->name('agreements.index');
Route::get('/agreements/create', [AgreementController::class, 'create'])->name('agreements.create');
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

// Quotations
Route::resource('quotations', QuotationController::class);
Route::get('/quotations', [QuotationController::class, 'list']);
Route::get('/quotations', [QuotationController::class, 'list'])->name('quotations.list');
Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
Route::put('/quotations/{id}/update-status', [QuotationController::class, 'updateStatus']);
Route::post('/quotations/{quotationId}/comments', [QuotationController::class, 'storeComment']);
Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
Route::put('/quotations/{id}/toggle-active', [QuotationController::class, 'toggleActive']);
Route::put('/quotations/{id}/mark-printed', [QuotationController::class, 'markPrinted']);
Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
Route::get('/quotations/{quotation}/print', [QuotationController::class, 'print'])
    ->name('quotations.print')
    ->middleware('auth');
Route::get('/quotations/{quotation_no}/pdf', [QuotationController::class, 'generatePDF'])->name('quotations.pdf');

// Invoices
Route::get('/invoices/list', [InvoiceController::class, 'list'])->name('invoices.list');
Route::resource('invoices', InvoiceController::class);
Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
Route::get('/quotations/{quotation_no}/invoices', [InvoiceController::class, 'getInvoicesByQuotation']);
Route::put('/invoices/{invoice}/update-status', [InvoiceController::class, 'updateStatus'])
    ->name('invoices.updateStatus');
Route::post('/invoices/send',[InvoiceController::class, 'sendInvoice'])->name('invoices.send');

// Receipts
Route::resource('receipts', ReceiptController::class);
Route::get('receipts/{id}/print', [ReceiptController::class, 'print'])->name('receipts.print');

// PaymentSchedule
Route::post('/payment-schedules/{id}/receipt', [PaymentScheduleController::class, 'createReceipt'])->name('payment-schedules.createReceipt');
Route::put('/payment-schedules/{id}', [PaymentScheduleController::class, 'update'])->name('payment-schedules.update');
Route::put('/payment-schedules/{invoice_no}/update-status', [PaymentScheduleController::class, 'updateStatus']);

// Customers
Route::get('/settings/customers', [CustomerController::class, 'index'])->name('customers.index'); // List all customers
Route::get('/api/customers', [CustomerController::class, 'apiIndex']);
Route::get('/settings/customers/create', [CustomerController::class, 'create'])->name('customers.create'); // Show form to create a new customer
Route::post('/settings/customers', [CustomerController::class, 'store'])->name('customers.store'); // Store new customer
Route::get('/settings/customers/show/{customer}', [CustomerController::class, 'show'])->name('customers.show');
Route::put('/settings/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::get('/settings/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
Route::delete('/settings/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy'); // Delete a customer
Route::put('/settings/customers/{customer}/toggle-active', [CustomerController::class, 'toggleActive'])
    ->name('customers.toggleActive');
Route::get('/activate-all-customers', function() {
    \App\Models\Customer::query()->update(['active' => true]);
    return 'All customers activated';
});

// Route::get('/settings/products', [ProductController::class, 'index'])->name('products.index');
// Route::get('/settings/products/create', [ProductController::class, 'create'])->name('products.create');
// Route::post('/settings/products', [ProductController::class, 'store'])->name('products.store');
// Route::get('/settings/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
// Route::post('/settings/products/{product}', [ProductController::class, 'update'])->name('products.update');
// Route::delete('/settings/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
Route::get('/pdfs/{filename}', [ProductController::class, 'viewPdf'])->where('filename', '.*')->name('pdf.view');
Route::post('/settings/products', [ProductController::class, 'getDepartments'])->name('products.index');
Route::put('/settings/products/{product}/toggleStatus', [ProductCommentController::class, 'store']);
Route::put('/settings//products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
    ->name('products.toggleStatus');
Route::resource('/settings/products', ProductController::class);
Route::get('/settings', [CustomerController::class, 'index'])->name('settings');

Route::get('/settings/customer-categories', [CustomerCategoryController::class, 'index'])->name('customerCategory.index');
Route::get('/settings/product-categories', [ProductCategoryController::class, 'index'])->name('productCategory.index');

// routes/api.php
Route::post('/quotations/send',[QuotationController::class, 'sendQuotation'])->name('quotations.send');
Route::get('/send-quotation-email', [QuotationEmailController::class, 'sendEmail']);

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Route::get('/dashboard', function () {
//     return Inertia::render('Welcome');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware(['auth'])->group(function () {
//     // Dashboard
//     Route::get('/dashboard', fn () => Inertia::render('Welcome'))->middleware(['verified'])->name('dashboard');

//     // Profile
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

//     // Agreements
//     Route::get('/agreements', [AgreementController::class, 'index'])->name('agreements.index');
//     Route::get('/agreements/create', [AgreementController::class, 'create'])->name('agreements.create');
//     Route::get('/agreements/{agreement_no}', [AgreementController::class, 'show'])->name('agreements.show');
//     Route::post('/agreements/store', [AgreementController::class, 'store'])->name('agreements.store');
//     Route::post('/agreements/upload', [AgreementController::class, 'upload'])->name('agreements.upload');
//     Route::put('/agreements/{agreement_no}', [AgreementController::class, 'update'])->name('agreements.update');
//     Route::get('/agreements/{agreement_no}/edit', [AgreementController::class, 'edit'])->name('agreements.edit');
//     Route::get('/quotations/{quotationId}/agreement', [QuotationController::class, 'getAgreementForQuotation']);
//     Route::get('/search-quotation', [AgreementController::class, 'searchQuotation']);
//     Route::get('/check-agreement-reference', [AgreementController::class, 'checkDuplicateReference']);

//     // Quotations
//     Route::resource('quotations', QuotationController::class);
//     Route::get('/quotations', [QuotationController::class, 'list'])->name('quotations.list');
//     Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
//     Route::put('/quotations/{id}/update-status', [QuotationController::class, 'updateStatus']);
//     Route::post('/quotations/{quotationId}/comments', [QuotationController::class, 'storeComment']);
//     Route::put('/quotations/{id}/toggle-active', [QuotationController::class, 'toggleActive']);
//     Route::put('/quotations/{id}/mark-printed', [QuotationController::class, 'markPrinted']);
//     Route::get('/quotations/{quotation}/print', [QuotationController::class, 'print'])->name('quotations.print');
//     Route::get('/quotations/{quotation_no}/pdf', [QuotationController::class, 'generatePDF'])->name('quotations.pdf');

//     // Quotations Email
//     Route::post('/quotations/send', [QuotationController::class, 'sendQuotation'])->name('quotations.send');
//     Route::get('/send-quotation-email', [QuotationEmailController::class, 'sendEmail']);

//     // Invoices
//     Route::resource('invoices', InvoiceController::class);
//     Route::get('/invoices/list', [InvoiceController::class, 'list'])->name('invoices.list');
//     Route::post('/api/invoices/filter', [InvoiceController::class, 'filter']);
//     Route::put('/invoices/{invoice}/update-status', [InvoiceController::class, 'updateStatus'])->name('invoices.updateStatus');
//     Route::post('/invoices/send', [InvoiceController::class, 'sendInvoice'])->name('invoices.send');
//     Route::get('/quotations/{quotation_no}/invoices', [InvoiceController::class, 'getInvoicesByQuotation']);

//     // Receipts
//     Route::resource('receipts', ReceiptController::class);
//     Route::get('receipts/{id}/print', [ReceiptController::class, 'print'])->name('receipts.print');

//     // Payment Schedules
//     Route::post('/payment-schedules/{id}/receipt', [PaymentScheduleController::class, 'createReceipt'])->name('payment-schedules.createReceipt');
//     Route::put('/payment-schedules/{id}', [PaymentScheduleController::class, 'update'])->name('payment-schedules.update');
//     Route::put('/payment-schedules/{invoice_no}/update-status', [PaymentScheduleController::class, 'updateStatus']);

//     // Customers
//     Route::prefix('settings/customers')->group(function () {
//         Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
//         Route::get('/create', [CustomerController::class, 'create'])->name('customers.create');
//         Route::post('/', [CustomerController::class, 'store'])->name('customers.store');
//         Route::get('/show/{customer}', [CustomerController::class, 'show'])->name('customers.show');
//         Route::put('/{customer}', [CustomerController::class, 'update'])->name('customers.update');
//         Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
//         Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
//         Route::put('/{customer}/toggle-active', [CustomerController::class, 'toggleActive'])->name('customers.toggleActive');
//     });
//     Route::get('/activate-all-customers', function () {
//         \App\Models\Customer::query()->update(['active' => true]);
//         return 'All customers activated';
//     });

//     // Products
//     Route::resource('/settings/products', ProductController::class);
//     Route::put('/settings/products/{product}/toggleStatus', [ProductCommentController::class, 'store']);
//     Route::put('/settings/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggleStatus');
//     Route::post('/settings/products', [ProductController::class, 'getDepartments'])->name('products.index');
//     Route::get('/pdfs/{filename}', [ProductController::class, 'viewPdf'])->where('filename', '.*')->name('pdf.view');

//     // Settings
//     Route::get('/settings', [CustomerController::class, 'index'])->name('settings');
//     Route::get('/settings/customer-categories', [CustomerCategoryController::class, 'index'])->name('customerCategory.index');
//     Route::get('/settings/product-categories', [ProductCategoryController::class, 'index'])->name('productCategory.index');

//     // API
//     Route::get('/api/customers', [CustomerController::class, 'apiIndex']);
// });

require __DIR__.'/auth.php';
