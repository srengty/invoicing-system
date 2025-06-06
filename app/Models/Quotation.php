<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quotation extends Model
{
    use HasFactory;
    protected $primaryKey = 'id'; // Explicitly set the primary key
    protected $keyType = 'int'; // Ensure it's an integer
    protected $fillable = [
        'printed_at',
        'quotation_no',
        'quotation_date',
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
        'printed_at' => 'datetime:Y-m-d H:i:s',
        'total' => 'double',
        'exchange_rate' => 'double',
        'quotation_date' => 'datetime:Y-m-d',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_quotation', 'quotation_no', 'product_id')
                    ->withPivot(['quantity', 'price', 'include_catalog'])
                    ->withTimestamps();
    }
    public function productQuotations():HasMany
    {
        return $this->hasMany(ProductQuotation::class, 'quotation_no', 'id');
    }
    public function invoices():HasMany
    {
        return $this->hasMany(Invoice::class, 'quotation_no', 'quotation_no');
    }

    // In Quotation.php (Quotation Model)
    public function agreement()
    {
        return $this->hasOne(Agreement::class, 'quotation_no', 'quotation_no');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_no', 'quotation_no');
    }

    public function comments()
    {
        return $this->hasMany(QuotationComment::class);
    }

    public function latestComment()
    {
        return $this->hasOne(QuotationComment::class)->latest();
    }

}

