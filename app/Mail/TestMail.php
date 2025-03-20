<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;


class TestMail extends Mailable
{
    public $data;
    public $filePath;

    public function __construct($data, $filePath)
    {
        $this->data = $data;
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->view('test-mail')
                    ->subject('Test Email from Laravel')
                    ->with('data', $this->data)
                    ->attach($this->filePath, [
                        'as' => 'your-pdf-file.pdf',
                        'mime' => 'application/pdf',
                        'disposition' => 'inline',
                        'id' => 'pdf_attachment',  // This is important to reference it in the HTML view
                    ]);
    }
}
