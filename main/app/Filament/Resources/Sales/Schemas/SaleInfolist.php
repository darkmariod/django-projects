<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SaleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('Sale #')
                    ->inlineLabel(),
                TextEntry::make('customer.name')
                    ->label('Customer')
                    ->inlineLabel(),
                TextEntry::make('user.name')
                    ->label('Salesperson')
                    ->inlineLabel(),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->inlineLabel(),
                TextEntry::make('notes')
                    ->label('Notes')
                    ->hidden(fn ($state) => empty($state))
                    ->inlineLabel(),

                TextEntry::make('saleItems')
                    ->label('Products')
                    ->getStateUsing(fn ($record) => $record->saleItems->map(fn ($item) => "{$item->product->name} × {$item->quantity} = $".number_format($item->subtotal, 2)
                    )->toArray())
                    ->bulleted(),

                TextEntry::make('subtotal')
                    ->label('Subtotal')
                    ->money('USD')
                    ->inlineLabel(),
                TextEntry::make('tax')
                    ->label('Tax (15%)')
                    ->money('USD')
                    ->inlineLabel(),
                TextEntry::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->inlineLabel(),
                TextEntry::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y h:i A')
                    ->inlineLabel(),
            ]);
    }
}
