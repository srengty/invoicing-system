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
        $customers = Customer::orderBy('created_at', 'desc')->get();
        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'customerCategories' => CustomerCategory::all(),
        ]);
    }

    public function apiIndex()
    {
        $customers = Customer::orderBy('name', 'asc')
            ->get(['id', 'name', 'code as customer_code']);

        return response()->json([
            'customers' => $customers
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
    public function store(Request $request, Customer $customer)
    {
        // Validate incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:customers,code,' . $customer->id . '|string|max:255',
            'credit_period' => 'required|numeric|min:0',
            'address' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:10|regex:/^0\d+$/',
            'contact_person' => 'nullable|string|max:255',
            'telegram_number' => 'nullable|string|max:10|regex:/^0\d+$/',
            'username' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
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
            'code' => 'required|unique:customers,code,' . $customer->id . '|string|max:255',
            'credit_period' => 'required|numeric|min:0|max:30',
            'address' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:10|regex:/^0\d+$/',
            'contact_person' => 'nullable|string|max:255' ,
            'telegram_number' => 'nullable|string|max:10|regex:/^0\d+$/',
            'username' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
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
        return redirect()->route('customers.index', $customer->id)->with('success', 'Customer updated successfully!');
    }

    // Delete customer
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index', $customer->id)->with('success', 'Customer deleted successfully!');
    }
    // Toggle active status for customers [Deactivate||Activate]
    public function toggleActive(Request $request, Customer $customer)
    {
        // Validate the incoming request data
        $data = $request->validate([
            'active' => 'required|boolean',
        ]);

        // Update the customer's active status
        $customer->active = $data['active'];
        $customer->save();
    }
    // Toggle active status for select customers [Deactivate||Activate]
    public function toggleActivation(Customer $customer)
{
    $customer->update(['active' => !$customer->active]);

    return response()->json([
        'success' => true,
        'active' => $customer->active
    ]);
}
}
