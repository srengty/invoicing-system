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
    public $filename;

    public function __construct($quotation, $pdfContent, $filename)
    {
        $this->quotation = $quotation;
        $this->pdfContent = $pdfContent;
        $this->filename = $filename;
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
            subject: 'ITC Finance - Quotation #'.$this->quotation->quotation_no,
            from: new Address('itcfinance168@gmail.com', 'ITC Finance'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quotation',
            with: [
                'quotation' => $this->quotation,
            ],
        );
    }

    public function attachments(): array
    {
        if ($this->pdfContent instanceof UploadedFile) {
            return [
                Attachment::fromPath($this->pdfContent->getRealPath())
                    ->as($this->filename)
                    ->withMime('application/pdf'),
            ];
        }

        // Handle when pdfContent is a string path or raw content
        return [
            Attachment::fromData(fn () => $this->pdfContent, $this->filename)
                ->withMime('application/pdf'),
        ];
    }

    // public function build()
    // {
    //     return $this->subject("Quotation #{$this->quotation->quotation_no}")
    //                 ->view('emails.quotation')
    //                 ->attach($this->pdfPath, [
    //                     'as' => 'quotation_'.$this->quotation->quotation_no.'.pdf',
    //                     'mime' => 'application/pdf',
    //                 ]);
    // }

    public function build()
    {
        return $this->view('emails.quotation')
            ->subject('Quotation PDF Attached')
            ->attachData($this->pdfContent, $this->filename, [
                'mime' => 'application/pdf',
            ])
            ->with([
                'quotation' => $this->quotation
            ]);
    }

}
