<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductCategoryController extends Controller
{
    public function index()
    {   
        $products = Product::all();
        $productCategories = Category::all();
        return Inertia::render('ProductCategories/Index',[
            'products' => $products,
            'productCategories' => $productCategories,
        ]);
    }
    public function create()
    {
        return Inertia::render('ProductCategories/Create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        Category::create($request->all());
        return redirect()->route('product-categories.index');
    }
    public function show(int $id)
    {
        return Inertia::render('ProductCategories/Show', [
            'category' => Category::find($id),
        ]);
    }
    public function edit(int $id)
    {
        return Inertia::render('ProductCategories/Edit', [
            'category' => Category::find($id),
        ]);
    }
    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required',
        ]);
        Category::find($id)->update($request->all());
        return redirect()->route('product-categories.index');
    }
    public function destroy(int $id)
    {
        Category::destroy($id);
        return redirect()->route('product-categories.index');
    }
}
