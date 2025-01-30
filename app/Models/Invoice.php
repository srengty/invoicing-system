<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

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

    // Relationship with Agreement
    public function agreement()
    {
        return $this->belongsTo(Agreement::class); // Assuming one-to-one relationship with Agreement
    }

    // Relationship with Quotation
    public function quotation()
    {
        return $this->belongsTo(Quotation::class); // Assuming one-to-one relationship with Quotation
    }

    // Relationship with Products (many-to-many with pivot table for quantities)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'invoice_product')
                    ->withPivot('quantity')  // If you're storing additional fields like quantity
                    ->withTimestamps();
    }
}

