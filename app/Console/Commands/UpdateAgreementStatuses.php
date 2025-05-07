<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateAgreementStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-agreement-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $agreements = Agreement::with('paymentSchedules.receipts')->get();
        foreach ($agreements as $agreement) {
            $status = $agreement->updateStatus();
            if ($agreement->status !== $status) {
                $agreement->status = $status;
                $agreement->save();
            }
        }

        $this->info('Agreement statuses updated successfully.');
    }

}
