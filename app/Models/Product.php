<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // The table associated with the model
    protected $table = 'products';

    // The attributes that are mass assignable
    protected $fillable = [
        'name',
        'code',        // Add the code attribute
        'unit',        // Add the unit attribute
        'price',
        'quantity',    // Add the quantity attribute
        'category',    // Add the category attribute
    ];

    // Optional: If you're working with timestamps, you can define the columns here
    public $timestamps = true;

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_product')
                    ->withPivot('quantity'); // Include quantity from the pivot table
    }
}
