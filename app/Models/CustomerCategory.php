<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCategory extends Model
{
    use HasFactory;
    protected $fillable = ['category_name_khmer', 'category_name_english', 'description_khmer', 'description_english'];
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
