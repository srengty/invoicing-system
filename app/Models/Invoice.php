<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    protected $fillable = [
        'invoice_no',
        'agreement_no',
        'quotation_no',
        'customer_id',
        'address',
        'phone_number',
        'email',
        'terms',
        'total',
        'total_usd',
        'exchange_rate',
        'status',
    ];

    protected $casts = [
        'quotation_date' => 'datetime',
        'total' => 'double',
        'exchange_rate' => 'double',
        'quotation_date' => 'datetime:Y-m-d',
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

    public function productQuotations():HasMany
    {
        return $this->hasMany(ProductQuotation::class, 'quotation_no', 'id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'invoice_product', 'invoice_no', 'product_id')
                    ->withPivot(['quantity', 'price', 'include_catalog'])  // If you're storing additional fields like quantity
                    ->withTimestamps();
    }
}

