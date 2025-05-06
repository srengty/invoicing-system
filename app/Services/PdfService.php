<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function generateQuotation(array $data)
    {
        $pdf = Pdf::loadView('quotations.pdf', $data);
        return $pdf->output();
    }
}
