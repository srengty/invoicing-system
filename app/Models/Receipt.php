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
        'installment_paid',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_no', 'invoice_no');
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_receipt', 'receipt_no', 'invoice_id', 'receipt_no', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }

    public function paymentSchedules()
    {
        return $this->belongsToMany(PaymentSchedule::class, 'payment_schedule_receipt', 'receipt_receipt_no', 'payment_schedule_id', 'receipt_no', 'id')->withPivot('paid_amount');;
    }
    

}
