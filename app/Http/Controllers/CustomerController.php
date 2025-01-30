<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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
        return Inertia::render('Customers/Create');
    }

    // Store a new customer
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'telegram_number' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_address' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_swift' => 'nullable|string|max:255',
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully!');
    }

    // Show customer details
    public function show($id)
    {
        $customer = Customer::findOrFail($id);

        return Inertia::render('Customers/Show', [
            'customer' => $customer,
        ]);
    }

    // Show edit form
    public function edit(Customer $customer)
    {
        // Return the edit view with the customer data using Inertia
        return Inertia::render('Customers/Edit', [
            'customer' => $customer
        ]);
    }

    // Update customer
    public function update(Request $request, Customer $customer)
    {
        // Validate incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'telegram_number' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_address' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_swift' => 'nullable|string|max:255',
        ]);

        // Update the customer with validated data
        $customer->update($validated);

        // Return an Inertia response for Inertia-based rendering
        return Inertia::render('Customers/Show', [
            'customer' => $customer,
            'message' => 'Customer updated successfully!',
        ]);
    }

    // Delete customer
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully!');
    }
}
