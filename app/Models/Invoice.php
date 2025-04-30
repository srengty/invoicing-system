<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $primaryKey = 'id'; // Explicitly set the primary key
    protected $keyType = 'int'; // Assuming invoice_no is the primary key
    protected $fillable = [
        'invoice_no',
        'invoice_date',
        'invoice_end_date',
        'agreement_no',
        'quotation_no',
        'customer_id',
        'address',
        'phone',
        'products',
        'start_date',
        'end_date',
        'total_usd',
        'grand_total',
        'exchange_rate',
        'terms',
        'status',
        'installment_paid',
        'paid_amount',
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'total' => 'double',
        'exchange_rate' => 'double',
        'invoice_date' => 'datetime:Y-m-d',
        'invoice_end_date' => 'datetime:Y-m-d',
    ];

    // Relationship with Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customer_category()
    {
        return $this->belongsTo(CustomerCategory::class);
    }

    // Relationship with Agreement
    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'agreement_no', 'agreement_no');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_no', 'quotation_no');
    }

    public function product_quotations()
    {
        return $this->belongsTo(ProductQuotation::class);
    }

    // Relationship with Products (many-to-many with pivot table for quantities)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'invoice_product', 'invoice_id', 'product_id')
                    ->withPivot('quantity', 'price', 'include_catalog', 'pdf_url');
    }

    public function invoiceComments()
    {
        return $this->hasMany(InvoiceComment::class);
    }

    public function invoices_product():HasMany
    {
        return $this->hasMany(InvoiceProduct::class, 'invoice_no', 'id');
    }


    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function getPaymentStatusAttribute()
    {
        // If the invoice has been fully paid (paid_amount >= grand_total)
        if ($this->paid_amount >= $this->grand_total) {
            return 'Fully Paid';
        }

        // If there has been some payment but not fully paid (paid_amount > 0 and < grand_total)
        if ($this->paid_amount > 0) {
            return 'Partially Paid';
        }

        // If payment has not been made and the invoice is overdue (compare invoice_due_date with current date)
        // Assume invoice_due_date is already set and represents the due date for payment
        if ($this->invoice_due_date && $this->invoice_due_date < now()) {
            return 'Overdue';
        }

        // If payment is still pending (no payment made, and it's not overdue)
        return 'Pending';
    }

    public function paymentSchedules()
    {
        return $this->belongsToMany(PaymentSchedule::class, 'invoices', 'invoice_no', 'payment_schedule_id');
    }

    public function getPaymentSchedules()
{
    // Fetch all active payment schedules (you can add additional filters as necessary)
    $paymentSchedules = PaymentSchedule::select('id', 'amount', 'short_description')
        ->where('status', 'Pending') // Optional: Filter by 'Pending' status, or adjust as needed
        ->get();

    // Return as JSON for the frontend
    return response()->json($paymentSchedules);
}

}

