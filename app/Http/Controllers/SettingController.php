<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Fetch data you want to display on the dashboard
        $productCount = Product::count(); // Example: Total products
        $customerCount = Customer::count(); // Example: Total customers

        return response()->json([
            'productCount' => $productCount,
            'customerCount' => $customerCount,
        ]);
    }
}

