<?php

namespace App\Filament\Resources\Sales\Pages;

use App\Filament\Resources\Sales\SaleResource;
use App\Models\Product;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditSale extends EditRecord
{
    protected static string $resource = SaleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $items = $data['saleItems'] ?? [];
        $stockErrors = [];

        // Validate stock for all items
        foreach ($items as $index => $item) {
            if (isset($item['product_id']) && isset($item['quantity'])) {
                $product = Product::find($item['product_id']);

                if ($product) {
                    if ($item['quantity'] > $product->stock) {
                        $stockErrors[] = "Product '{$product->name}': requested {$item['quantity']}, available {$product->stock}";
                    }
                }
            }
        }

        // If there are stock errors and trying to confirm, throw validation error
        if (! empty($stockErrors) && ($data['status'] ?? 'draft') === 'confirmed') {
            throw ValidationException::withMessages([
                'saleItems' => $stockErrors,
            ]);
        }

        // Calculate totals
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += floatval($item['subtotal'] ?? 0);
        }

        $tax = round($subtotal * 0.15, 2);
        $total = round($subtotal + $tax, 2);

        $data['subtotal'] = $subtotal;
        $data['tax'] = $tax;
        $data['total'] = $total;

        // If confirming but has stock issues, keep as draft
        if (! empty($stockErrors)) {
            $data['status'] = 'draft';
        }

        return $data;
    }
}
