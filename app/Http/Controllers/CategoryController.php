<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        return Inertia::render('Categories/Index');
    }
    public function create()
    {
        return Inertia::render('Categories/Create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        Category::create($request->all());
        return redirect()->route('categories.index');
    }
    public function show(int $id)
    {
        return Inertia::render('Categories/Show', [
            'category' => Category::find($id),
        ]);
    }
    public function edit(int $id)
    {
        return Inertia::render('Categories/Edit', [
            'category' => Category::find($id),
        ]);
    }
    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required',
        ]);
        Category::find($id)->update($request->all());
        return redirect()->route('categories.index');
    }
    public function destroy(int $id)
    {
        Category::destroy($id);
        return redirect()->route('categories.index');
    }
}
