<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentSchedule;
use Carbon\Carbon;

class CheckPaymentStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:check-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update payment schedule statuses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');

        PaymentSchedule::where('status', '!=', 'PAID')
            ->whereDate('due_date', '<', $today)
            ->where('status', '!=', 'PAST DUE')
            ->update(['status' => 'PAST DUE']);

        $this->info('Payment statuses checked and updated successfully.');
    }
}
