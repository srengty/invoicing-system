<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;

class TelegramController extends Controller
{
    public function webhook(Request $request)
    {
        $data = $request->all();
        Log::info('Telegram webhook received:', $data); // Debug

        $chatId = $data['message']['chat']['id'] ?? null;
        $username = $data['message']['from']['username'] ?? null;
        $text = $data['message']['text'] ?? null;
        
        // OPTIONAL: If customer sends phone number via contact
        $phone = $data['message']['contact']['phone_number'] ?? null;

        if ($chatId) {
            // Handle /start command
            if ($text === '/start') {
                $this->handleStartCommand($chatId, $username, $phone);
                return response()->json(['ok' => true]);
            }

            if ($phone) {
                // Match by phone number
                $cleanPhone = ltrim($phone, '+');
                $customer = Customer::where('phone_number', 'like', "%$cleanPhone")->first();
            } elseif ($username) {
                // Match by Telegram username
                $customer = Customer::where('username', $username)->first();
            }

            if (!empty($customer)) {
                $customer->telegram_chat_id = $chatId;
                $customer->save();

                Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => "Hi @$username, your Telegram is now linked.",
                ]);
            }
        }
        Log::info('Chat ID:', ['chatId' => $chatId]);
        Log::info('Username:', ['username' => $username]);
        Log::info('Phone:', ['phone' => $phone]);
        Log::info('Text:', ['text' => $text]);


        return response()->json(['ok' => true]);
    }

    protected function handleStartCommand($chatId, $username, $phone = null)
    {
        $customer = null;
        
        if ($phone) {
            // Try to find by phone number first
            $cleanPhone = ltrim($phone, '+');
            $customer = Customer::where('phone_number', 'like', "%$cleanPhone")->first();
        }
        
        if (!$customer && $username) {
            // Fall back to username if phone not found or not provided
            $customer = Customer::where('username', $username)->first();
        }

        if ($customer) {
            // Update chat ID if customer found
            $customer->telegram_chat_id = $chatId;
            $customer->save();

            Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
                'chat_id' => $chatId,
                'text' => "Hi @$username! Please share your phone number to link your account.",
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [
                            [
                                'text' => 'Share Phone Number',
                                'request_contact' => true
                            ]
                        ]
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ])
            ]);
        } else {
            // Customer not found in database
            Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
                'chat_id' => $chatId,
                'text' => "Hi @$username! We couldn't find your account. Please contact support.",
            ]);
        }
    }
}