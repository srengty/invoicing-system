<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    /** @use HasFactory<\Database\Factories\DivisionFactory> */
    use HasFactory;
    protected $fillable = [
        'division_name_khmer',
        'division_name_english',
        'description_khmer',
        'description_english',
    ];
}
