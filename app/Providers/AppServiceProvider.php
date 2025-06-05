<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Agreement;
use App\Models\Quotation;
use App\Models\Invoice;
use App\Models\Product;
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
            "userRoles"=> session()->get("roles",[]),
            'dueAgreementsCount' => function () {
                if (auth()->check()) {
                    $today = Carbon::now()->startOfDay();
                    $thresholdDays = 14;

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
            },
            'pendingQuotationsCount' => function () {
                if (auth()->check()) {
                    return Quotation::where('status', 'Pending')->count();
                }
                return 0;
            },
            'pendingInvoicesCount' => function () {
                if (auth()->check()) {
                    return Invoice::where('status', 'Pending')->count();
                }
                return 0;
            },
            'pendingItemsCount' => function () {
                if (auth()->check()) {
                    return Product::where('status', 'Pending')->count();
                }
                return 0;
            },
        ]);
    }
}
