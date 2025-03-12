<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductComment;
use Illuminate\Http\Request;

class ProductCommentController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'comment' => 'required|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        // ✅ Create a comment for the product
        $comment = new ProductComment([
            'comment' => $request->comment,
        ]);

        $product->comments()->save($comment);

        // ✅ Update the product's status
        $product->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Product status updated with comment.',
            'comment' => $comment,
        ]);
    }
}

