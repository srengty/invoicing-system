<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Division;
use App\Models\ProductComment;
use App\Models\Product;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['comments' => function ($query) {
            $query->latest();
        }])->orderBy('created_at', 'desc')->get();
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
            'price' => 'required|numeric|min:100',
            'category_id' => 'required|integer|min:0',
            'desc' => 'nullable|string|max:255',
            'desc_kh' => 'nullable|string|max:255',
            'remark' => 'nullable|string|max:255',
            'acc_code' => 'required|string|max:255',
            'division_id' => 'required|integer|max:255',
            'pdf' => 'nullable|file|mimes:pdf|max:2048',
            'status' => 'pending',
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
            'price' => 'required|numeric|min:100',
            'acc_code' => 'required|string',
            'desc' => 'nullable|string',
            'desc_kh' => 'nullable|string',
            'remark' => 'nullable|string',
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

    // Call deparment api
    public function getDepartments(){
        // 
        $client = new Client();
        
        // Make the GET request to fetch divisions
        $response = $client->request('POST', 'https://dev.itc.edu.kh/api/departments/', [
            'headers' => [
                'Authorization' => 'Bearer KuTw6xVJ4ZW0Z9RQFyS1l9mzrOU5XM2tpdE2ub6LJHgliufbbug7fZGs92Ht7KOj',
                'Content-Type' => 'application/json'
            ]
        ]);

        // Decode the response into an array
        $departments = json_decode($response->getBody(), true);
        
        // Use Inertia to pass the data to the Vue component
        return Inertia::render('Products/Index', [
            'departments' => $departments
              // Pass the data as props to the Vue page
        ]);
        dd($departments);
    }
    

    public function toggleStatus(Request $request, Product $product)
    {

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'comment' => 'nullable|string',
        ]);

        // ✅ Update the product status
        $product->update(['status' => $validated['status']]);

        $comment = null;

        // ✅ Save or update comment if provided
        if (!empty($validated['comment'])) {
            $comment = ProductComment::updateOrCreate(
                ['product_id' => $product->id], // Ensure one comment per product
                ['comment' => $validated['comment']]
            );
        }
    }

}
