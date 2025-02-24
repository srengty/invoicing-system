<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quotation extends Model
{
    use HasFactory;
    protected $primaryKey = 'quotation_no'; // Explicitly set the primary key
    public $incrementing = false;  // Since we're manually generating the ID
    protected $keyType = 'int'; // Ensure it's an integer
    protected $fillable = [
        'quotation_no',
        'quotation_date',
        'customer_id',
        'address',
        'phone_number',
        'terms',
        'total',
        'status',
    ];

    protected $casts = [
        'quotation_date' => 'datetime',
        'total' => 'double',
        'quotation_date' => 'datetime:Y-m-d',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_quotation', 'quotation_no', 'product_id')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
    public function productQuotations():HasMany
    {
        return $this->hasMany(ProductQuotation::class, 'quotation_no', 'quotation_no');
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

}

