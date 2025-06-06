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
        'hdStatus',
        'rmStatus',
        'installment_paid',
        'paid_amount',
        'receipt_no',
        'payment_schedule_id'
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

    public function hdComments()
    {
        return $this->hasMany(InvoiceHdComment::class);
    }

    public function rmComments()
    {
        return $this->hasMany(InvoiceRmComment::class);
    }

    public function invoices_product():HasMany
    {
        return $this->hasMany(InvoiceProduct::class, 'invoice_no', 'id');
    }

    public function getPaymentStatusAttribute()
    {
        if ($this->paid_amount >= $this->grand_total) {
            return 'Fully Paid';
        }

        if ($this->paid_amount > 0) {
            return 'Partially Paid';
        }

        if ($this->invoice_due_date && $this->invoice_due_date < now()) {
            return 'Overdue';
        }

        return 'Pending';
    }

    public function paymentSchedules()
    {
        return $this->belongsToMany(PaymentSchedule::class, 'invoice_payment_schedule');
        
    }


    public function getPaymentSchedules()
    {
        $paymentSchedules = PaymentSchedule::select('id', 'amount', 'short_description')
            ->where('status', 'Pending') // Optional: Filter by 'Pending' status, or adjust as needed
            ->get();

        return response()->json($paymentSchedules);
    }

    // In Invoice.php model
    public function paymentSchedule()
    {
        return $this->belongsTo(PaymentSchedule::class);
    }
    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'invoice_no', 'invoice_no');
    }

    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'agreement_no', 'agreement_no');
    }

    public function progressPayments()
    {
        return $this->hasMany(Agreement::class);
    }

    public function invoiceReceipts()
    {
        return $this->belongsToMany(Receipt::class, 'invoice_receipt', 'invoice_id', 'receipt_id');
    }

}

