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
        'code',
        'unit',
        'price',
        'quantity',
        'category',
    ];

    public $timestamps = true;

    // Example relationship if needed
    public function quotations()
    {
        return $this->belongsToMany(Quotation::class, 'quotation_product')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}
