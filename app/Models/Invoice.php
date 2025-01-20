<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    // Define the table name if it doesn't follow the default plural convention
    protected $table = 'invoice_table';

    // Specify fillable fields for mass assignment
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'invoice_date',
        'total_amount',
        'is_paid',
    ];

    // Define relationships if necessary
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
