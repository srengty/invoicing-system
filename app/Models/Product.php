<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'code',
        'unit',
        'price',
        'quantity',
        'category',
    ];

    public $timestamps = true;

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_product')
                    ->withPivot('quantity'); // Include quantity from the pivot table
    }
    // Example relationship if needed
    public function quotations()
    {
        return $this->belongsToMany(Quotation::class, 'product_quotation', 'product_id', 'quotation_no')
                    ->withPivot('quantity', 'price', 'name', 'unit')
                    ->withTimestamps();
    }

    public function productQuotations()
    {
        return $this->hasMany(ProductQuotation::class, 'quotation_no', 'quotation_no')
                    ->withPivot('quantity', 'price', 'name', 'unit');
    }

}
