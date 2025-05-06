<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class SetTelegramWebhook extends Command
{
    protected $signature = 'telegram:webhook {url} {--ignore-ssl}';
    protected $description = 'Set Telegram webhook URL';

    public function handle()
    {
        $url = $this->argument('url');
        $ignoreSsl = $this->option('ignore-ssl');

        $this->info("Setting webhook to: {$url}");
        $this->info("SSL Verification: " . ($ignoreSsl ? "DISABLED" : "ENABLED"));

        try {
            $telegram = app(TelegramService::class);
            $result = $telegram->setWebhook($url, $ignoreSsl);

            if ($result['success']) {
                $this->info("Webhook set successfully!");
                $this->line(json_encode($result['response'], JSON_PRETTY_PRINT));
                return Command::SUCCESS;
            }

            $this->error("Failed to set webhook:");
            $this->line(json_encode($result['response'] ?? $result['error'], JSON_PRETTY_PRINT));

            // Suggest ngrok if local development
            if (app()->environment('local')) {
                $this->newLine();
                $this->warn("Local Development Tip:");
                $this->line("1. Run ngrok: <comment>ngrok http 80</comment>");
                $this->line("2. Use the HTTPS URL provided by ngrok");
                $this->line("3. Try with SSL verification disabled:");
                $this->line("   <comment>php artisan telegram:webhook https://your-ngrok-url.ngrok.io/telegram/webhook --ignore-ssl</comment>");
            }

            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error("Critical error:");
            $this->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
