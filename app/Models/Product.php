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
        'name_kh',
        'code',
        'unit',
        'price',
        'quantity',
        'category',
        'division_id',
        'acc_code',
        'desc',
        'desc_kh'
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
        return $this->belongsToMany(Quotation::class, 'product_quotation', 'product_id', 'id')
                    ->withPivot('quantity', 'price', 'name', 'unit')
                    ->withTimestamps();
    }

    public function productQuotations()
    {
        return $this->hasMany(ProductQuotation::class, 'product_id', 'id')
                    ->withPivot('quantity', 'price', 'unit');
    }

}
