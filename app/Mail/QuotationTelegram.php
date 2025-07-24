<?php

namespace App\Mail;

use App\Models\Quotation;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;

class QuotationTelegram
{
    protected $quotation;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    public function render(): string
    {
        return ViewFacade::make('telegrambot.quotation', [
            'quotation' => $this->quotation
        ])->render();
    }

    public function getMessage(): string
    {
        return $this->render();
    }
}
