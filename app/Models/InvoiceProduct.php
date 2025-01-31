<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    use HasFactory;

    // Specify the table name (optional if it's the plural form of the model name)
    protected $table = 'invoice_product';

    // Specify the fillable fields (to prevent mass assignment vulnerability)
    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity',
    ];

    /**
     * Define the relationship with the Invoice model.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Define the relationship with the Product model.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
