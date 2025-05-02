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
    protected $appends = ['status'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // In Agreement.php (Agreement Model)
    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_no', 'quotation_no');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'invoice_no', 'invoice_no');
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
    public function getTotalScheduledPaymentAttribute()
    {
        return $this->paymentSchedules()->sum('amount');
    }

    public function getStatusAttribute()
    {
        $today = now();
        $endDate = \Carbon\Carbon::createFromFormat('d/m/Y', $this->end_date);

        return $endDate->lt($today) ? 'Closed' : 'Open';
    }

    // AgreementController method to fetch agreement with payment schedules
    public function getAgreementData($agreement_no)
    {
        $agreement = Agreement::with('paymentSchedules')->find($agreement_no);

        if (!$agreement) {
            return response()->json(['message' => 'Agreement not found'], 404);
        }

        // Calculate paid amount from payment schedules
        $paid_amount = $agreement->paymentSchedules->sum('amount');

        return;
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'agreement_no', 'agreement_no');
    }

    public function paymentSchedules()
    {
        return $this->hasMany(PaymentSchedule::class, 'agreement_no', 'agreement_no');
    }

}
