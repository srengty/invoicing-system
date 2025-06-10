<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use App\Models\Invoice; // Change from Quotation to Invoice
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Http;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;  // Change from $quotation to $invoice
    public $pdfPath;  // The PDF path will remain the same

    public function __construct(Invoice $invoice, UploadedFile $pdfPath)
    {
        $this->invoice = $invoice;
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {  
  
        return new Envelope(
            subject: 'ITC Finance - Invoice Details',  // Update subject for invoice
            from: new Address('itcfinance168@gmail.com', 'ITC Finance'),
        );
    }

    public function content(): Content
    {
       
        return new Content(
            view: 'invoice-mail',  // Ensure this matches your Blade template
            with: [
                'invoice' => $this->invoice,  // Pass the invoice instead of quotation
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath->getRealPath())
                      ->as($this->pdfPath->getClientOriginalName())
                      ->withMime($this->pdfPath->getMimeType()),
        ];
    }
}
