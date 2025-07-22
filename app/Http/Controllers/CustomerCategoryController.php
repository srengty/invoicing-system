<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::all();
        $customerCategories = CustomerCategory::all(); // Fetch customer categories

        return Inertia::render('CustomerCategories/Index', [
            'customers' => $customers,
            'customerCategories' => $customerCategories, // Pass customer categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CustomerCategories/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name_khmer' => 'required|string|max:255',
            'category_name_english' => 'required|string|max:255',
            'description_khmer' => 'nullable|string',
            'description_english' => 'nullable|string',
        ]);

        CustomerCategory::create($validated);

        return redirect()->route('customerCategories.index')
            ->with('success', 'Customer category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerCategory $customerCategory)
    {
        return Inertia::render('CustomerCategories/Show', [
            'customerCategory' => $customerCategory,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerCategory $customerCategory)
    {
        return Inertia::render('CustomerCategories/Edit', [
            'customerCategory' => $customerCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerCategory $customerCategory)
    {
        $validated = $request->validate([
            'category_name_khmer' => 'required|string|max:255',
            'category_name_english' => 'required|string|max:255',
            'description_khmer' => 'nullable|string',
            'description_english' => 'nullable|string',
        ]);

        $customerCategory->update($validated);

        return redirect()->route('customerCategories.index')
            ->with('success', 'Customer category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerCategory $customerCategory)
    {
        $customerCategory->delete();
        return redirect()->route('customerCategories.index')
            ->with('success', 'Customer category deleted successfully.');
    }
}
