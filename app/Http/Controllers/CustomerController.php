<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    // Display all customers
    public function index()
    {
        $customers = Customer::all();
        return Inertia::render('Customers/Index', [
            'customers' => $customers,
        ]);
    }

    // Show create form
    public function create()
    {
        return Inertia::render('Customers/Create', [
            'customerCategories' => CustomerCategory::all(),
        ]);
    }

    // Store a new customer
    public function store(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|integer|max:5000000',
            'credit_period' => 'nullable|numeric',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20', // Changed from 'phone_number' to 'phone'
            'telegram_number' => 'nullable|string|max:20', // Changed from 'telegram_number' to 'telegram'
            'website' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_address' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_swift' => 'nullable|string|max:255',
            'customer_category_id' => 'required|exists:customer_categories,id',
        ]);

        // Create the customer
        Customer::create($validated);

        // Redirect to the customers index page with a success message
        return redirect()->route($request['redirect_route']??'customers.index')->with('success', 'Customer created successfully!');
    }

    // Show customer details
    public function show($id)
    {
        $customer = Customer::with('customerCategory')->findOrFail($id);
        return Inertia::render('Customers/Show', [
            'customer' => $customer,
            'customerCategory' => $customer->customerCategory,
        ]);
    }

    // Show edit form
    public function edit(int $customer_id)
    {
        $customer = Customer::with('customerCategory')->findOrFail($customer_id);
        return Inertia::render('Customers/Edit', [
            'customer' => $customer,
            'customerCategories' => CustomerCategory::all(),
        ]);
    }

    // Update customer
    public function update(Request $request, Customer $customer)
    {
        // Validate incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|integer|max:5000000',
            'credit_period' => 'nullable|integer',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20', // Changed from 'phone_number' to 'phone'
            'telegram_number' => 'nullable|string|max:20', // Changed from 'telegram_number' to 'telegram'
            'website' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_address' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_swift' => 'nullable|string|max:255',
            'customer_category_id' => 'required|exists:customer_categories,id',
        ]);

        // Update the customer with validated data
        $customer->update($validated);

        // Redirect to the customer's show page with a success message
        return redirect()->route('customers.show', $customer->id)->with('success', 'Customer updated successfully!');
    }

    // Delete customer
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully!');
    }
    
}