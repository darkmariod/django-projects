<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\SaleItem;

class SaleItemObserver
{
    public function created(SaleItem $saleItem): void
    {
        $this->recalculateSale($saleItem);
    }

    public function updated(SaleItem $saleItem): void
    {
        $this->recalculateSale($saleItem);
    }

    public function deleted(SaleItem $saleItem): void
    {
        $this->recalculateSale($saleItem);
    }

    protected function recalculateSale(SaleItem $saleItem): void
    {
        $sale = Sale::find($saleItem->sale_id);
        if ($sale) {
            $sale->recalculateTotals();
        }
    }
}
