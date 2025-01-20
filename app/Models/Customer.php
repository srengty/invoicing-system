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
        'code',
        'address',
        'email',
        'phone_number',
        'telegram_number',
        'website',
        'bank_name',
        'bank_address',
        'bank_account_name',
        'bank_account_number',
        'bank_swift',
    ];

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

}

