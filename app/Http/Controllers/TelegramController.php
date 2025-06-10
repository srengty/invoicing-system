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
        \Log::info('Telegram Webhook Payload:', $request->all());

        $message = $data['message'] ?? null;

        if (!$message) {
            return response()->json(['ok' => true]);
        }

        $chatId = $message['chat']['id'] ?? null;
        $username = $message['from']['username'] ?? null;
        $text = $message['text'] ?? '';
        $phone = $message['contact']['phone_number'] ?? null;

        if ($chatId) {
            if ($text === '/start') {
                $this->handleStartCommand($chatId, $username, $phone);
                return response()->json(['ok' => true]);
            }

            $customer = null;

            // Try phone if sent
            if ($phone) {
                $cleanPhone = ltrim($phone, '+');
                $customer = Customer::where('phone_number', 'like', "%$cleanPhone")->first();
            }

            // Fallback to username if no phone or no match
            if (!$customer && $username) {
                $customer = Customer::where('username', $username)->first();
            }

            if ($customer) {
                $customer->telegram_chat_id = $chatId;
                $customer->save();

                Http::withoutVerifying()->post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => "Hi @$username, your Telegram is now linked.",
                ]);
            } else {
                // Send fallback message if customer is not found
                Http::withoutVerifying()->post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => "Sorry @$username, we couldn't find your account. Please use /start or contact support.",
                ]);
            }
        }

        return response()->json(['ok' => true]);
    }

    protected function handleStartCommand($chatId, $username, $phone = null)
    {
        $customer = null;

        if ($phone) {
            $cleanPhone = ltrim($phone, '+');
            $customer = Customer::where('phone_number', 'like', "%$cleanPhone")->first();
        }

        if (!$customer && $username) {
            $customer = Customer::where('username', $username)->first();
        }

        if ($customer) {
            $customer->telegram_chat_id = $chatId;
            $customer->save();

            Http::withoutVerifying()->post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
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
            Http::withoutVerifying()->post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
                'chat_id' => $chatId,
                'text' => "Hi @$username! We couldn't find your account. Please contact support.",
            ]);
        }
    }
}
