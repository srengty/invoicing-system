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
        $today = Carbon::today();
        $dueSoonThreshold = $today->copy()->addDays(13);

        // Update past due payments
        PaymentSchedule::where('status', '!=', 'PAID')
            ->whereDate('due_date', '<', $today)
            ->update(['status' => 'PAST_DUE']);

        // Update due soon payments (due within 13 days, including today)
        PaymentSchedule::where('status', '!=', 'PAID')
            ->whereBetween('due_date', [$today, $dueSoonThreshold])
            ->update(['status' => 'DUE_SOON']);

        // Reset payments beyond 13 days to UPCOMING if they're not PAID
        PaymentSchedule::where('status', '!=', 'PAID')
            ->whereDate('due_date', '>', $dueSoonThreshold)
            ->update(['status' => 'UPCOMING']);

        $this->info('Payment statuses checked and updated successfully.');
    }
}
