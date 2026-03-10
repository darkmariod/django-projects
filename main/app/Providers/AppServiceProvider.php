<?php

namespace App\Providers;

use App\Events\SaleConfirmed;
use App\Listeners\CreateStockMovementFromSale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Observers\SaleItemObserver;
use App\Observers\StockMovementObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        StockMovement::observe(StockMovementObserver::class);
        SaleItem::observe(SaleItemObserver::class);

        Event::listen(
            SaleConfirmed::class,
            CreateStockMovementFromSale::class
        );
    }
}
