<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'user_id',
        'role',
        'status',
        'comment',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
