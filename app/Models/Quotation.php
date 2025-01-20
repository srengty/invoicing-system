<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    /** @use HasFactory<\Database\Factories\QuotationFactory> */
    use HasFactory;
    // protected $fillable = [
    //     'quotation_no',
    //     'quotation_date',
    //     'address',
    //     'quotation_doc',
    //     'amount_no_tax',
    //     'tax',
    //     'status',
    //     'customer_id',
    // ];
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
    /**
     * The attributes that should be cast to native types.
     *
    * @var array
    */
    protected $casts = [
        'quotation_date' => 'datetime',
        'total' => 'double',
        'tax' => 'double',
        'grand_total' => 'double',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
