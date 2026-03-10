<?php

namespace App\Filament\Resources\Sales\Tables;

use App\Models\Product;
use App\Models\StockMovement;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width('60px'),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Seller')
                    ->toggleable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make()->label(''),
                EditAction::make()->label(''),
                Action::make('cancelSale')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'confirmed')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // Load sale items
                        $record->load('saleItems');

                        foreach ($record->saleItems as $item) {
                            $product = Product::find($item->product_id);

                            if ($product) {
                                // Restore stock
                                $product->increment('stock', $item->quantity);

                                // Create stock movement
                                StockMovement::create([
                                    'product_id' => $product->id,
                                    'type' => 'in',
                                    'quantity' => $item->quantity,
                                    'reference' => "Sale #{$record->id} cancelled",
                                    'user_id' => Auth::id(),
                                    'notes' => "Sale #{$record->id} cancelled - {$product->name}",
                                ]);
                            }
                        }

                        // Update sale status
                        $record->update(['status' => 'cancelled']);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
