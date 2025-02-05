<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    /** @use HasFactory<\Database\Factories\AgreementFactory> */
    use HasFactory;
    protected $pimaryKey  = 'agreement_no';
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
    //protected $dateFormat = 'd/m/Y';
    protected function casts(){
        return [
            'agreement_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }
    protected function agreementDate():Attribute
    {
        return Attribute::make(get:fn(string $value)=>(new Carbon($value))->format('d/m/Y'));
    }
    protected function startDate():Attribute
    {
        return Attribute::make(get:fn(string $value)=>(new Carbon($value))->format('d/m/Y'));
    }
    protected function endDate():Attribute
    {
        return Attribute::make(get:fn(string $value)=>(new Carbon($value))->format('d/m/Y'));
    }
}
