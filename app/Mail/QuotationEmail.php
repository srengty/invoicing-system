<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use App\Models\Quotation;
use Illuminate\Mail\Mailables\Attachment;

class QuotationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;
    public $pdfPath;

    public function __construct(Quotation $quotation, $pdfPath)
    {
        $this->quotation = $quotation;
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ITC Finance - Quotation Details',
            from: new Address('itcfinance168@gmail.com', 'ITC Finance'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'test-mail', // Ensure this matches your Blade template
            with: [
                'quotation' => $this->quotation,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                      ->as('quotation.pdf')
                      ->withMime('application/pdf'),
        ];
    }
}