<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceComment extends Model
{   
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'user_id',
        'role',
        'status',
        'comment',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
