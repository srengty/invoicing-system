<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Mail\QuotationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class QuotationEmailController extends Controller
{
    public function sendEmail( $quotation)
    {   
        // Create a Quotation instance for testing (You can retrieve an actual Quotation record from DB)
        $quotation = new Quotation();
        $quotation->id = 1; // Assign a test id or fetch it from the database if necessary
        $quotation->name = 'John Doe';
        $quotation->total_amount = 1000;
        // Add other necessary quotation fields here

        // Set the path to the PDF file
        $filePath = storage_path('app/public/pdfs/1740554871_67bec27779d17_DourngDariya_CV.pdf');

        // Send the email with the Quotation and the PDF path
        Mail::to('monopich1823@gmail.com')->send(new QuotationEmail($quotation, $filePath));

        return 'Quotation email sent with attachment!';
    }
}
