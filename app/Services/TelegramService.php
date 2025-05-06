<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;
    public string $apiUrl;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');

        if (empty($this->botToken)) {
            throw new \RuntimeException('Telegram bot token not configured');
        }

        if (!preg_match('/^\d{8,10}:[a-zA-Z0-9_-]{35}$/', $this->botToken)) {
            throw new \RuntimeException('Invalid Telegram bot token format');
        }

        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/";
    }


    public function setWebhook(string $url, bool $ignoreSsl = false): array
    {
        try {
            $client = Http::withOptions([
                'verify' => !$ignoreSsl,
                'timeout' => 30,
            ]);

            $response = $client->post($this->apiUrl . 'setWebhook', [
                'url' => $url,
                'drop_pending_updates' => true
            ]);

            return [
                'success' => $response->successful(),
                'response' => $response->json(),
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error("Telegram webhook setup failed: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function sendMessage($chatId, $message)
    {
        return Http::post($this->apiUrl.'sendMessage', [
            'chat_id' => $chatId,
            'text' => $message
        ]);
    }

    public function sendDocument($chatId, $documentPath, $caption = '')
    {
        try {
            if (!file_exists($documentPath)) {
                throw new \Exception("Document file not found");
            }

            $response = Http::withOptions([
                'verify' => app()->environment('production'), // Verify SSL only in production
                'timeout' => 30,
            ])->attach(
                'document',
                file_get_contents($documentPath),
                basename($documentPath)
            )->post($this->apiUrl . 'sendDocument', [
                'chat_id' => $chatId,
                'caption' => $caption
            ]);

            if (!$response->successful()) {
                throw new \Exception("Telegram API error: " . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error("Telegram send failed: " . $e->getMessage());
            throw $e; // Re-throw for controller to handle
        }
    }

    public function isValidPhoneNumber($phoneNumber)
    {
        // Remove all non-digit characters
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Check if it's a valid international format (starting with +)
        if (preg_match('/^\+[1-9]\d{9,14}$/', $phoneNumber)) {
            return true;
        }

        // Or check for local format (without +)
        return preg_match('/^[1-9]\d{9,14}$/', $cleaned);
    }

    public function formatPhoneNumberForTelegram($phoneNumber)
    {
        // Remove all non-digit characters
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);

        // If it starts with country code (e.g., 855 for Cambodia)
        if (strlen($cleaned) > 9) {
            return $cleaned;
        }

        // Add default country code if missing (adjust for your needs)
        return '855' . ltrim($cleaned, '0');
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }
}
