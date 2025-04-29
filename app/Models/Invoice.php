<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $primaryKey = 'id'; // Explicitly set the primary key
    protected $keyType = 'int'; // Assuming invoice_no is the primary key
    protected $fillable = [
        'invoice_no',
        'invoice_date',
        'agreement_no',
        'quotation_no',
        'customer_id',
        'address',
        'phone',
        'products',
        'start_date',
        'end_date',
        'total_usd',
        'grand_total',
        'exchange_rate',
        'terms',
        'status',
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'total' => 'double',
        'exchange_rate' => 'double',
        'invoice_date' => 'datetime:Y-m-d',
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
    return $this->belongsToMany(
        Product::class,       // Related model
        'invoice_product',    // Pivot table name
        'invoice_no',         // Foreign key on pivot (matches invoice_no in pivot)
        'product_id',         // Related key on pivot (links to products.id)
        'invoice_no',         // Local key (matches invoice_no in invoices table)
        'id'                  // Related key (products.id)
    )->withPivot(['quantity', 'price', 'include_catalog', 'pdf_url']);
}


    public function invoiceComments()
{
    return $this->hasMany(InvoiceComment::class);
}

    public function invoices_product():HasMany
    {
        return $this->hasMany(InvoiceProduct::class, 'invoice_no', 'id');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}

