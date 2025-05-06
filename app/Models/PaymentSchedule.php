<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSchedule extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentScheduleFactory> */
    use HasFactory;
   protected $fillable = [
    'agreement_no',
    'due_date',
    'amount',
    'paid_amount',
    'status',
    'percentage',
    'short_description',
    'currency',
    'paid_amount',
];
    public function agreement():BelongsTo
    {
        return $this->belongsTo(Agreement::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'payment_schedule_id', 'id');
    }

    protected $dateFormat = 'Y-m-d';
    protected function casts(){
        return [
            'due_date' => 'date',
        ];
    }
    protected function dueDate(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => Carbon::parse($value)->format('d/m/Y'),
            set: fn($value) => Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d')
        );
    }

    public function getPaidAmountAttribute()
    {
        return $this->receipts->sum('paid_amount');
    }

    public function getIsPaidAttribute()
    {
        return $this->paid_amount >= $this->amount;
    }

// In PaymentSchedule model
    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->paid_amount > $model->amount) {
                throw new \Exception('Paid amount cannot exceed scheduled amount');
            }
        });
    }

    public function updateStatus()
    {
        $this->status = $this->determineStatus();
        return $this;
    }

    protected function determineStatus()
    {
        if ($this->paid_amount >= $this->amount) {
            return 'PAID';
        } elseif ($this->paid_amount > 0) {
            return 'PARTIALLY_PAID';
        }

        $dueDate = \Carbon\Carbon::createFromFormat('d/m/Y', $this->due_date);
        return $dueDate->isPast() ? 'PAST_DUE' : 'UPCOMING';
    }

    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_payment_schedule');
    }

}
