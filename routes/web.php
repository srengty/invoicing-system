<?php

use App\Http\Controllers\AgreementController;

use App\Http\Controllers\CustomerController;
 
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
 
use App\Http\Controllers\QuotationController;
 
use App\Http\Controllers\ProfileController;
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

 
Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
 
 
Route::get('/quotations', [QuotationController::class, 'list'])->name('quotations.list');
Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
 
Route::get('/invoices/show', [InvoiceController::class, 'show'])->name('invoices.show');

Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index'); // List all customers
Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create'); // Show form to create a new customer
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store'); // Store new customer
Route::get('/customers/show/{customer}', [CustomerController::class, 'show'])->name('customers.show');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit'); // Update an existing customer
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy'); // Delete a customer

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create'); 
Route::post('/products', [ProductController::class, 'store'])->name('products.store'); 
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit'); 
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update'); 
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy'); 



Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
