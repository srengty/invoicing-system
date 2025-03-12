<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductComment extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'comment', 'user_name'];

    // ✅ Define the relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
