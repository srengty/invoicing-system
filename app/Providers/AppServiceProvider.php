<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Agreement;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
    //     if($this->app->environment('production')) {
    //         URL::forceScheme('https');
    //     }
    //     Vite::prefetch(concurrency: 3);
    // }

    public function boot()
    {
        Inertia::share([
            'dueAgreementsCount' => function () {
                if (auth()->check()) { // Only calculate if user is logged in
                    $today = Carbon::now()->startOfDay();
                    $thresholdDays = 13;

                    return Agreement::with('paymentSchedules')
                        ->whereHas('paymentSchedules', function ($query) use ($today, $thresholdDays) {
                            $query->where(function ($q) use ($today) {
                                $q->whereDate('due_date', '<', $today)
                                ->where(function ($inner) {
                                    $inner->whereNull('status')
                                            ->orWhere('status', '!=', 'PAID')
                                            ->orWhereColumn('paid_amount', '<', 'amount');
                                });
                            })
                            ->orWhere(function ($q) use ($today, $thresholdDays) {
                                $q->whereDate('due_date', '>=', $today)
                                ->whereDate('due_date', '<=', $today->copy()->addDays($thresholdDays))
                                ->where(function ($inner) {
                                    $inner->whereNull('status')
                                            ->orWhere('status', '!=', 'PAID')
                                            ->orWhereColumn('paid_amount', '<', 'amount');
                                });
                            });
                        })
                        ->count();
                }
                return 0;
            }
        ]);
    }
}
