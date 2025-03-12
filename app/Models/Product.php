<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\asset;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'name_kh',
        'code',
        'unit',
        'price',
        'category_id',
        'division_id',
        'acc_code',
        'desc',
        'desc_kh',
        'pdf_url',
        'remark',
        'status'
    ];

    protected $attributes = [
        'status' => 'pending', // ✅ Default to pending
    ];

    public $timestamps = true;

    /**
     * Get the category associated with the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the division associated with the product.
     */
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Many-to-Many relationship with Invoice.
     */
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_product')
                    ->withPivot('quantity') // Include quantity from the pivot table
                    ->withTimestamps();
    }

    /**
     * Many-to-Many relationship with Quotations.
     */
    public function quotations()
    {
        return $this->belongsToMany(Quotation::class, 'product_quotation', 'product_id', 'id')
                    ->withPivot('quantity', 'price', 'name', 'unit')
                    ->withTimestamps();
    }

    /**
     * One-to-Many relationship with ProductQuotation.
     */
    public function productQuotations()
    {
        return $this->hasMany(ProductQuotation::class, 'product_id', 'id')
                    ->withPivot('quantity', 'price', 'unit');
    }

    /**
     * Get the full URL of the uploaded PDF file.
     */
    public function getPdfUrlAttribute()
    {
        if (!$this->attributes['pdf_url']) {
            return null;
        }

        // If the stored PDF URL is already a full URL, return it as-is
        if (filter_var($this->attributes['pdf_url'], FILTER_VALIDATE_URL)) {
            return $this->attributes['pdf_url'];
        }

        // Otherwise, prepend it with the correct storage path
        return asset('storage/pdfs/' . $this->attributes['pdf_url']);
    }

    public function comments()
    {
        return $this->hasMany(ProductComment::class);
    }

}
