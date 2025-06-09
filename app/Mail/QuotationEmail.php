<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use App\Models\Quotation;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailer;

class QuotationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;
    public $pdfPath;

    public function __construct(Quotation $quotation, UploadedFile $pdfPath)
    {
        $this->quotation = $quotation;
        $this->pdfPath = $pdfPath;
    }
    // public function build()
    // {
    //     return $this->subject('Here is your PDF')
    //     ->from(new Address('itcfinance168@gmail.com', 'ITC Finance'))
    //                 ->view('test-mail')
    //                 ->with([
    //                     'quotation' => $this->quotation
    //                 ])
    //                 ->attach($this->pdfPath->getRealPath(), [
    //                     'as' => $this->pdfPath->getClientOriginalName(),
    //                     'mime' => $this->pdfPath->getMimeType(),
    //                 ]);
    // }
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
            view: 'emails.quotation', // Ensure this matches your Blade template
            with: [
                'quotation' => $this->quotation,
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
