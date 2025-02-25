<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parameter extends Model
{
    /** @use HasFactory<\Database\Factories\ParameterFactory> */
    use HasFactory;
    protected $fillable = [
        'parameter_name',
        'parameter_value',
    ];
}
