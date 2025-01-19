<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Agreement;

class QuotationController extends Controller
{

    public function list()
    {
        return Inertia::render('Quotations/List', [
            'agreements' => Agreement::with('customer')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

}
