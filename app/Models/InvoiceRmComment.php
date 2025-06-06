<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceRmComment extends Model
{
    use HasFactory;

    // Define the table name (optional, since Laravel will infer it from the model name)
    protected $table = 'invoice_rm_comments';

    // The attributes that are mass assignable
    protected $fillable = [
        'invoice_id',
        'role',
        'status',
        'comment',
    ];

    // Define the relationship to the Invoice model
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
