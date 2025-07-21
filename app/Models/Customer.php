<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'contact_person',
        'code',
        'credit_period',
        'address',
        'email',
        'phone_number',
        'telegram_number',
        'username',
        'credit_period',
        'website',
        'bank_name',
        'bank_address',
        'bank_account_name',
        'bank_account_number',
        'bank_swift',
        'customer_category_id',
    ];

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function customerCategory()
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_category_id');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

}

