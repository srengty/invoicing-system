<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Agreement;
use App\Models\Quotation;
use App\Models\Invoice;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

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
        if(App::environment("production")){
            URL::forceScheme('https');
        }
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
                    $pendinQuotation = Quotation::where('status', 'Pending')->count();
                    $reviseQuotation = Quotation::where('status', 'Revise')->count();
                    return $pendinQuotation + $reviseQuotation;
                }
                return 0;
            },
            'pendingInvoicesCount' => function () {
                if (auth()->check()) {
                    $pendinInvoices = Invoice::where('status', 'Pending')->count();
                    $reviseInvoices = Invoice::where('status', 'Revise')->count();
                    return $pendinInvoices + $reviseInvoices;

                }
                return 0;
            },
            'hdPendingCount' => function () {
                return Auth::check()
                    ? Invoice::whereIn('hdStatus', ['Pending','Revise'])->count()
                    : 0;
            },

            'rmPendingCount' => function () {
                return Auth::check()
                    ? Invoice::where('hdStatus', 'approved')
                            ->whereIn('rmStatus', ['Pending','Revise'])
                            ->count()
                    : 0;
            },

            'directorPendingCount' => function () {
                return Auth::check()
                    ? Invoice::where('rmStatus', 'approved')
                            ->whereIn('status', ['Pending','Revise'])
                            ->count()
                    : 0;
            },
            'pendingItemsCount' => function () {
                if (auth()->check()) {
                    $pendinItems = Product::where('status', 'Pending')->count();
                    $reviseItems  = Product::where('status', 'Rejected')->count();
                    return $pendinItems + $reviseItems;
                }
                return 0;
            },
        ]);
    }
}
