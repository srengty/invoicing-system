<?php
use App\Http\Controllers\AgreementController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\CustomerCategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductCommentController;
use App\Http\Controllers\QuotationEmailController;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
Route::get('/agreements', [AgreementController::class, 'index'])->name('agreements.index');
Route::get('/agreements/create', [AgreementController::class, 'create'])->name('agreements.create');
Route::get('/agreements/show/{id}', [AgreementController::class, 'show'])->name('agreements.show');
Route::post('/agreements/store', [AgreementController::class, 'store'])->name('agreements.store');
Route::post('/agreements/upload', [AgreementController::class, 'upload'])->name('agreements.upload');
Route::put('/agreements/{agreement_no}', [AgreementController::class, 'update'])->name('agreements.update');
Route::get('/agreements/{agreement_no}/edit', [AgreementController::class, 'edit'])->name('agreements.edit');
Route::put('/agreements/{agreement_no}', [AgreementController::class, 'update'])->name('agreements.update');
Route::get('/quotations/{quotationId}/agreement', [QuotationController::class, 'getAgreementForQuotation']);
Route::post('/api/invoices/filter', [InvoiceController::class, 'filter']);
Route::get('/search-quotation', [AgreementController::class, 'searchQuotation']);
Route::get('/check-agreement-reference', [AgreementController::class, 'checkDuplicateReference']);

Route::resource('quotations', QuotationController::class);
Route::get('/quotations', [QuotationController::class, 'list']);
Route::get('/quotations', [QuotationController::class, 'list'])->name('quotations.list');
Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
Route::put('/quotations/{id}/update-status', [QuotationController::class, 'updateStatus']);
Route::post('/quotations/{quotationId}/comments', [QuotationController::class, 'storeComment']);
// Route::get('/quotations/{quotation_no}', [QuotationController::class, 'show'])->name('quotations.show'); // For printing
Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
// Route::put('/quotations/{id}', [QuotationController::class, 'update'])->name('quotations.update');

Route::resource('invoices', InvoiceController::class);
Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
// Route::get('/invoices/show', [InvoiceController::class, 'show'])->name('invoices.show');
Route::get('/quotations/{quotation_no}/invoices', [InvoiceController::class, 'getInvoicesByQuotation']);

Route::get('/settings/customers', [CustomerController::class, 'index'])->name('customers.index'); // List all customers
Route::get('/settings/customers/create', [CustomerController::class, 'create'])->name('customers.create'); // Show form to create a new customer
Route::post('/settings/customers', [CustomerController::class, 'store'])->name('customers.store'); // Store new customer
Route::get('/settings/customers/show/{customer}', [CustomerController::class, 'show'])->name('customers.show');
Route::put('/settings/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::get('/settings/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
Route::delete('/settings/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy'); // Delete a customer
Route::put('/settings/customers/{customer}/toggle-active', [CustomerController::class, 'toggleActive'])
    ->name('customers.toggleActive');
// routes/web.php
Route::get('/activate-all-customers', function() {
    \App\Models\Customer::query()->update(['active' => true]);
    return 'All customers activated';
});

Route::get('/settings/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/settings/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/settings/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/settings/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
// Route::post('/settings/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/settings/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
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
Route::get('/send-test-email', function () {
    $data = [
        'name' => 'John Doe',
        'message' => 'This is a test email from Laravel.',
    ];

    // Assuming the PDF file is located in the storage path
    $filePath = storage_path('app/public/pdfs/1740554871_67bec27779d17_DourngDariya_CV.pdf');

    Mail::to('monopich1823@gmail.com')->send(new TestMail($data, $filePath));

    return 'Test email sent with attachment!';
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
