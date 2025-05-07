<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receipt extends Model
{
    use HasFactory;
    protected $primaryKey  = 'receipt_no';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'invoice_no',
        'receipt_no',
        'receipt_date',
        'customer_id',
        'customer_code',
        'purpose',
        'paid_amount',
        'amount_in_words',
        'payment_method',
        'payment_reference_no',
        'payment_schedule_id',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_no', 'invoice_no');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function agreement():BelongsTo
    {
        return $this->belongsTo(Agreement::class);
    }

    public function paymentSchedule()
    {
        return $this->belongsTo(PaymentSchedule::class);
    }
    

}
