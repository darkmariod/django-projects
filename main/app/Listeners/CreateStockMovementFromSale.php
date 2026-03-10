<?php

namespace App\Listeners;

use App\Events\SaleConfirmed;
use App\Models\StockMovement;

class CreateStockMovementFromSale
{
    public function handle(SaleConfirmed $event): void
    {
        $sale = $event->sale;

        foreach ($sale->saleItems as $item) {
            StockMovement::create([
                'product_id' => $item->product_id,
                'type' => 'out',
                'quantity' => $item->quantity,
                'reference' => "Sale #{$sale->id}",
                'user_id' => $sale->user_id,
                'notes' => "Sale #{$sale->id} confirmed - {$item->product->name}",
            ]);
        }
    }
}
