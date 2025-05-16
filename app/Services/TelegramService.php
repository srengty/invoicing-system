<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $botToken;
    protected string $apiUrl;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');

        if (empty($this->botToken)) {
            throw new \RuntimeException('Telegram bot token not configured');
        }

        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/";
    }

    /**
     * Send a simple text message
     */
    public function sendMessage(string $chatId, string $text): array
    {
        $response = Http::post($this->apiUrl . 'sendMessage', [
            'chat_id' => $chatId,
            'text'    => $text,
        ]);

        if (!$response->successful()) {
            Log::error('Telegram sendMessage failed', ['response' => $response->body()]);
        }

        return $response->json();
    }

    /**
     * Send a PDF document from raw binary content to any chat
     */
    public function sendDocumentRaw(string $chatId, string $pdfContent, string $filename, string $caption = ''): array
    {
        $response = Http::withOptions(['timeout' => 30])
            ->attach('document', $pdfContent, $filename)
            ->post($this->apiUrl . 'sendDocument', [
                'chat_id' => $chatId,
                'caption' => $caption,
            ]);

        if (!$response->successful()) {
            Log::error('Telegram sendDocument failed', ['response' => $response->body()]);
            throw new \Exception('Telegram API error: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Send PDF to a user by their phone number
     */
    public function sendPdfToPhoneNumber(string $phoneNumber, string $pdfContent, string $filename, string $caption = ''): array
    {
        if (! $this->isValidPhoneNumber($phoneNumber)) {
            throw new \InvalidArgumentException('Invalid phone number format: ' . $phoneNumber);
        }

        $chatId = $this->formatPhoneNumberForTelegram($phoneNumber);
        return $this->sendDocumentRaw($chatId, $pdfContent, $filename, $caption);
    }

    /**
     * Send PDF to a channel or group by username or ID
     */
    public function sendPdfToChannel(string $channel, string $pdfContent, string $filename, string $caption = ''): array
    {
        if (empty($channel)) {
            throw new \InvalidArgumentException('Telegram channel is required');
        }

        // channel should be "@finances_itc" or "-1001234567890"
        return $this->sendDocumentRaw($channel, $pdfContent, $filename, $caption);
    }

    /**
     * Validate international phone number formats (E.164)
     */
    public function isValidPhoneNumber(string $phoneNumber): bool
    {
        return (bool) preg_match('/^\+[1-9]\d{9,14}$/', $phoneNumber);
    }

    /**
     * Format local phone number into E.164 (default country: Cambodia +855)
     */
    public function formatPhoneNumberForTelegram(string $phoneNumber): string
    {
        $clean = preg_replace('/\D+/', '', $phoneNumber);

        // If already starts with country code
        if (str_starts_with($clean, '855') && strlen($clean) >= 11) {
            return '+' . $clean;
        }

        // Remove leading zeros
        $local = ltrim($clean, '0');

        return '+855' . $local;
    }

    /**
     * Get base API URL
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }
}
