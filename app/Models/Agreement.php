<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    /** @use HasFactory<\Database\Factories\AgreementFactory> */
    use HasFactory;
    protected $fillable = [
        'agreement_no',
        'agreement_ref_no',
        'agreement_date',
        'address',
        'agreement_doc',
        'start_date',
        'end_date',
        'amount_no_tax',
        'tax',
        'status',
        'customer_id',
        'quotation_no',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
