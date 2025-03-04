<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Division;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        $categories = Category::select('id', 'category_name_english')->get();
        $divisions = Division::select('id', 'division_name_english')->get();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'divisions' => $divisions,
        ]);
    }

    public function create()
    {
        return Inertia::render('Products/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_kh' => 'required|string|max:255',
            'code' => 'required|string|unique:products,code',
            'unit' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|integer|min:0',
            'desc' => 'nullable|string|max:255',
            'desc_kh' => 'nullable|string|max:255',
            'remark' => 'required|string|max:255',
            'acc_code' => 'required|string|max:255',
            'division_id' => 'required|integer|max:255',
            'pdf' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($request->hasFile('pdf')) {
            $file = $request->file('pdf');
            $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            $file->storeAs('pdfs', $fileName, 'public');
            $validated['pdf_url'] = $fileName;
        } else {
            $validated['pdf_url'] = null;
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', [
            'product' => $product
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'division_id' => 'required',
            'category_id' => 'required',
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'name_kh' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'acc_code' => 'required|string',
            'desc' => 'nullable|string',
            'desc_kh' => 'nullable|string',
            'remark' => 'required|string',
            'pdf' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        if ($request->hasFile('pdf')) {
            if ($product->pdf_url) {
                Storage::disk('public')->delete('pdfs/' . $product->pdf_url);
            }

            $file = $request->file('pdf');
            $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            $file->storeAs('pdfs', $fileName, 'public');
            $data['pdf_url'] = $fileName;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('error', 'There was an error deleting the product.');
        }
    }


    public function viewPdf($filename)
    {
        $path = storage_path("app/public/pdfs/{$filename}");

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        return Response::file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }

}
