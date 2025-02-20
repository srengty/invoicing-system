<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $primaryKey = 'invoice_no'; // Assuming invoice_no is the primary key
    protected $fillable = [
        'invoice_no',
        'agreement_no',
        'quotation_no',
        'customer_id',
        'address',
        'phone',
        'start_date',
        'end_date',
        'grand_total',
        'status',
    ];

    // Relationship with Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customer_category()
    {
        return $this->belongsTo(CustomerCategory::class);
    }

    // Relationship with Agreement
    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'agreement_no', 'agreement_no');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_no', 'quotation_no');
    }

    public function product_quotations()
    {
        return $this->belongsTo(ProductQuotation::class);
    }

    // Relationship with Products (many-to-many with pivot table for quantities)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'invoice_product', 'invoice_no', 'product_id')
                    ->withPivot('quantity')  // If you're storing additional fields like quantity
                    ->withTimestamps();
    }
}

