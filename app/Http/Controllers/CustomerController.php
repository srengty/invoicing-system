<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        return Inertia::render('Customers/Index');
    }

    public function create()
    {
        return Inertia::render('Customers/Create');
    }
}
