<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = ['quotation_id', 'name', 'unit_price'];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
