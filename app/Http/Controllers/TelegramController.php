<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function webhook(Request $request)
    {
        $update = $request->all();

        // Handle incoming messages
        if (isset($update['message'])) {
            $chatId = $update['message']['chat']['id'];
            $text = $update['message']['text'] ?? '';

            // Simple command handling
            if ($text === '/start') {
                $this->telegramService->sendMessage($chatId,
                    "Hello! This is your quotation bot. You'll receive quotations here.");
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
