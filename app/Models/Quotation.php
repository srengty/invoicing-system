<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;
    protected $primaryKey = 'quotation_no';
    protected $fillable = [
        'quotation_no',
        'quotation_date',
        'customer_id',
        'address',
        'phone_number',
        'terms',
        'total',
        'tax',
        'grand_total',
        'status',
        'customer_status',
    ];

    protected $casts = [
        'quotation_date' => 'datetime',
        'total' => 'double',
        'tax' => 'double',
        'grand_total' => 'double',
        'quotation_date' => 'datetime:Y-m-d',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Define a many-to-many relationship with Product
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_quotation')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}

