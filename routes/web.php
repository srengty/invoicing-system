<?php
use App\Http\Controllers\AgreementController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Models\Customer;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
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
Route::post('/agreements/store', [AgreementController::class, 'store'])->name('agreements.store');
Route::post('/agreements/upload', [AgreementController::class, 'upload'])->name('agreements.upload');

Route::resource('quotations', QuotationController::class);
Route::get('/quotations', [QuotationController::class, 'list']);
Route::get('/quotations', [QuotationController::class, 'list'])->name('quotations.list');
Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');

Route::resource('invoices', InvoiceController::class);
Route::get('/invoices/show', [InvoiceController::class, 'show'])->name('invoices.show');

Route::get('/settings/customers', [CustomerController::class, 'index'])->name('customers.index'); // List all customers
Route::get('/settings/customers/create', [CustomerController::class, 'create'])->name('customers.create'); // Show form to create a new customer
Route::post('/settings/customers', [CustomerController::class, 'store'])->name('customers.store'); // Store new customer
Route::get('/settings/customers/show/{customer}', [CustomerController::class, 'show'])->name('customers.show');
Route::put('/settings/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::get('/settings/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
Route::delete('/settings/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy'); // Delete a customer

Route::get('/settings/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/settings/products/create', [ProductController::class, 'create'])->name('products.create'); 
Route::post('/settings/products', [ProductController::class, 'store'])->name('products.store'); 
Route::get('/settings/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit'); 
Route::put('/settings/products/{product}', [ProductController::class, 'update'])->name('products.update'); 
Route::delete('/settings/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy'); 

Route::get('/settings', [CustomerController::class, 'index'])->name('settings');



Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
