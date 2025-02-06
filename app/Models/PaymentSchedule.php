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
        'short_description',
        'percentage',
        'amount',
        'currency',
        'status',
    ];
    public function agreement():BelongsTo
    {
        return $this->belongsTo(Agreement::class);
    }
    protected $dateFormat = 'Y-m-d';
    protected function casts(){
        return [
            'due_date' => 'date',
        ];
    }
    protected function dueDate():Attribute
    {
        return Attribute::make(get:fn(string $value)=>(new Carbon($value))->format('d/m/Y'));
    }
}
