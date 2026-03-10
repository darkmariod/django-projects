<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'user_id',
        'subtotal',
        'tax',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Calculate totals from sale items
     */
    public function calculateTotals(): void
    {
        // Reload sale items from database to get the latest data
        $this->load('saleItems');

        $subtotal = (float) $this->saleItems->sum('subtotal');
        $tax = round($subtotal * 0.15, 2);
        $total = round($subtotal + $tax, 2);

        $this->subtotal = $subtotal;
        $this->tax = $tax;
        $this->total = $total;
    }

    /**
     * Recalculate and save totals
     */
    public function recalculateTotals(): void
    {
        $this->calculateTotals();
        $this->saveQuietly();
    }

    /**
     * Create stock movements when sale is confirmed
     */
    public function createStockMovements(): void
    {
        // Validate stock first
        $errors = [];

        foreach ($this->saleItems as $item) {
            $availableStock = $item->product->stock;

            // Calculate already sold quantity (from other confirmed sales)
            $soldQty = \App\Models\StockMovement::where('product_id', $item->product_id)
                ->where('type', 'out')
                ->sum('quantity');

            $currentStock = $availableStock - $soldQty;

            if ($currentStock < $item->quantity) {
                $errors[] = "Product '{$item->product->name}': only {$currentStock} available, requested {$item->quantity}";
            }
        }

        if (! empty($errors)) {
            throw new \Exception("Stock validation failed:\n".implode("\n", $errors));
        }

        // Create stock movements
        foreach ($this->saleItems as $item) {
            StockMovement::create([
                'product_id' => $item->product_id,
                'type' => 'out',
                'quantity' => $item->quantity,
                'reference' => "Sale #{$this->id}",
                'user_id' => $this->user_id,
                'notes' => "Sale #{$this->id} confirmed - {$item->product->name}",
            ]);
        }
    }

    protected static function boot()
    {
        parent::boot();

        // After creating
        static::created(function (Sale $sale) {
            $sale->recalculateTotals();
        });

        // After updating
        static::updated(function (Sale $sale) {
            $sale->recalculateTotals();
        });

        // When status changes to confirmed
        static::updated(function (Sale $sale) {
            if ($sale->wasChanged('status') && $sale->status === 'confirmed') {
                // Create stock movements directly
                $sale->createStockMovements();
            }
        });
    }
}
