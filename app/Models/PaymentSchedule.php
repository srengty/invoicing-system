<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSchedule extends Model
{
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
    ];

    protected $dateFormat = 'Y-m-d';

    protected function casts()
    {
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

    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'agreement_no', 'agreement_no');
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_payment_schedule');
    }

    public function receipts()
    {
        return $this->belongsToMany(
            Receipt::class,
            'payment_schedule_receipt',
            'payment_schedule_id',
            'receipt_receipt_no',
            'id',
            'receipt_no'
        );
    }

    public function getTotalPaidAmountAttribute()
    {
        return $this->receipts->sum('paid_amount');
    }

    public function getIsPaidAttribute()
    {
        return $this->total_paid_amount >= $this->amount;
    }

    // public function getRemainingAmountAttribute()
    // {
    //     return max(0, $this->amount - $this->total_paid_amount);
    // }

    public function updateStatus()
    {
        $this->status = $this->determineStatus();
        $this->save();
        return $this;
    }

    public function determineStatus()
    {
        $dueDate = Carbon::createFromFormat('d/m/Y', $this->due_date);
        $today = Carbon::today();

        if ($this->total_paid_amount >= $this->amount) {
            return 'PAID';
        } elseif ($this->total_paid_amount > 0) {
            return 'PARTIALLY_PAID';
        }

        if ($dueDate->isPast()) {
            return 'PAST_DUE';
        }

        $daysUntilDue = $today->diffInDays($dueDate, false);
        if ($daysUntilDue >= 0 && $daysUntilDue <= 13) {
            return 'DUE_SOON';
        }

        return 'UPCOMING';
    }


    // protected static function booted()
    // {
    //     static::saving(function ($model) {
    //         if ($model->paid_amount > $model->amount) {
    //             throw new \Exception('Paid amount cannot exceed scheduled amount');
    //         }
    //     });
    // }
}
