<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductDivisionController extends Controller
{
    public function index()
    {
        return Inertia::render('ProductDivisions/Index');
    }
    
    public function create()
    {
        return Inertia::render('ProductDivisions/Create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        Division::create($request->all());
        return redirect()->route('product-divisions.index');
    }
    
    public function show(int $id)
    {
        return Inertia::render('ProductDivisions/Show', [
            'division' => Division::find($id),
        ]);
    }
    
    public function edit(int $id)
    {
        return Inertia::render('ProductDivisions/Edit', [
            'division' => Division::find($id),
        ]);
    }
    
    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required',
        ]);
        Division::find($id)->update($request->all());
        return redirect()->route('product-divisions.index');
    }
    
    public function destroy(int $id)
    {
        Division::destroy($id);
        return redirect()->route('product-divisions.index');
    }
}

