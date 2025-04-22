<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agreement extends Model
{
    /** @use HasFactory<\Database\Factories\AgreementFactory> */
    use HasFactory;
    protected $primaryKey  = 'agreement_no';
    protected $fillable = [
        'agreement_no',
        'agreement_ref_no',
        'agreement_date',
        'address',
        'agreement_doc',
        'start_date',
        'end_date',
        'amount',
        'status',
        'customer_id',
        'quotation_no',
        'payment_schedules',
        'short_description',
        'currency',
        'attachments',
    ];
     // Add the $appends property here
    protected $appends = [
        'status',
        'total_progress_payment',
        'due_payment',
        'total_progress_payment_percentage',
        'is_fully_paid',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // In Agreement.php (Agreement Model)
    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_no', 'quotation_no');
    }


    // protected $dateFormat = 'Y-m-d';
    protected function casts(){
        return [
            'agreement_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }
    protected function agreementDate():Attribute
    {
        return Attribute::make(
            get:fn(string $value)=>(new Carbon($value))->format('d/m/Y'),
            set:fn($value)=>//(new Carbon($value))->format('Y-m-d')
            (Carbon::createFromFormat('d/m/Y',$value))
        );
    }
    protected function startDate():Attribute
    {
        return Attribute::make(get:fn(string $value)=>(new Carbon($value))->format('d/m/Y'),
            set:fn($value)=>//(new Carbon($value))->format('Y-m-d')
            (Carbon::createFromFormat('d/m/Y',$value))
        );
    }
    protected function endDate():Attribute
    {
        return Attribute::make(get:fn(string $value)=>(new Carbon($value))->format('d/m/Y'),
            set:fn($value)=>//(new Carbon($value))->format('Y-m-d')
            (Carbon::createFromFormat('d/m/Y',$value))
        );
    }
    public function paymentSchedules():HasMany
    {
        return $this->hasMany(PaymentSchedule::class, 'agreement_no');
    }
    public function getTotalScheduledPaymentAttribute()
    {
        return $this->paymentSchedules()->sum('amount');
    }

    public function getTotalProgressPaymentAttribute()
    {
        return $this->paymentSchedules()
            ->where('status', 'Paid')
            ->sum('amount');
    }

    public function getDuePaymentAttribute()
    {
        return $this->amount - $this->total_scheduled_payment;
    }

    public function getTotalProgressPaymentPercentageAttribute()
    {
        if ($this->amount <= 0) return 0;
        return ($this->total_scheduled_payment / $this->amount) * 100;
    }

    public function getIsFullyPaidAttribute()
    {
        return abs($this->amount - $this->total_progress_payment) < 1;
    }

    public function getStatusAttribute()
    {
        $today = now();
        $endDate = Carbon::createFromFormat('d/m/Y', $this->end_date);

        if ($this->is_fully_paid && $this->due_payment <= 0) {
            return 'Closed';
        }

        if ($today->gt($endDate)) {
            return 'Abnormal Closed';
        }

        return 'Open';
    }
}
