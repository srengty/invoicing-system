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
        
        Log::info('Telegram Webhook Payload:', ['data' => $data]);

        $message = $data['message'] ?? null;

        if (!$message) {
            return response()->json(['ok' => true]);
        }

        $chatId = $message['chat']['id'] ?? null;
        $username = $message['from']['username'] ?? null;
        $text = $message['text'] ?? '';

        if ($chatId) {
            // Handle /start command
            if ($text === '/start') {
                $this->handleStartCommand($chatId, $username);
                return response()->json(['ok' => true]);
            }

            // Lookup user by username
            $customer = null;

            if ($username) {
                $customer = Customer::where('username', $username)->first();
            }

            if ($customer) {
                // Update Telegram chat ID
                $customer->telegram_chat_id = $chatId;
                $customer->save();

                // Send confirmation message to the user
                Http::withoutVerifying()->post("https://api.telegram.org/bot" . config("app.telegram_token") . "/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => "Hi @$username, your Telegram is now linked.",
                ]);
            } else {
                // Send fallback message if customer is not found
                Http::withoutVerifying()->post("https://api.telegram.org/bot" . config("app.telegram_token") . "/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => "Sorry @$username, we couldn't find your account. Please use /start or contact support.",
                ]);
            }
        }

        return response()->json(['ok' => true]);
    }

    protected function handleStartCommand($chatId, $username)
    {
        $customer = null;

        // Lookup user by username
        if ($username) {
            $customer = Customer::where('username', $username)->first();
        }

        if ($customer) {
            // Link the Telegram chat ID to the customer
            $customer->telegram_chat_id = $chatId;
            $customer->save();

            // Send message asking for phone number (if needed)
            Http::withoutVerifying()->post("https://api.telegram.org/bot" . config("app.telegram_token"). "/sendMessage", [
                'chat_id' => $chatId,
                'text' => "Hi @$username! Your Telegram is now linked. Please contact support if you need further assistance.",
            ]);
        } else {
            // Send message if no matching user found
            Http::withoutVerifying()->post("https://api.telegram.org/bot" . config("app.telegram_token") . "/sendMessage", [
                'chat_id' => $chatId,
                'text' => "Hi @$username! We couldn't find your account. Please contact support.",
            ]);
        }
    }
}
