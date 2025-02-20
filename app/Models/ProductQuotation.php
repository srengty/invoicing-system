<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductQuotation extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural form of the model
    protected $table = 'product_quotation';

    // Allow these fields to be mass assignable
    protected $fillable = [
        'quotation_no',
        'product_id',
        'name',
        'unit',
        'quantity',
        'price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_no', 'quotation_no');
    }
}
