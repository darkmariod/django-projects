<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Models\Product;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->label('Customer')
                    ->required()
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),

                Radio::make('status')
                    ->label('Status')
                    ->inline()
                    ->options([
                        'draft' => 'Draft',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('draft'),

                Textarea::make('notes')
                    ->label('Notes')
                    ->rows(2)
                    ->nullable(),

                Repeater::make('saleItems')
                    ->label('Sale Items')
                    ->relationship()
                    ->addActionLabel('Add Product')
                    ->schema([
                        Select::make('product_id')
                            ->label('Product')
                            ->required()
                            ->options(function () {
                                return Product::all()->mapWithKeys(function ($product) {
                                    return [$product->id => "{$product->name} (Stock: {$product->stock})"];
                                })->toArray();
                            })
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        $set('price', $product->price);
                                        $set('quantity', 1);
                                        $set('subtotal', $product->price);
                                        $set('available_stock', $product->stock);
                                    }
                                } else {
                                    $set('available_stock', 0);
                                }
                            })
                            ->columnSpan(2),
                        TextInput::make('quantity')
                            ->label('Qty')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $price = floatval($get('price') ?? 0);
                                $qty = intval($state ?? 1);
                                $set('subtotal', round($price * $qty, 2));

                                // Validate stock
                                $available = intval($get('available_stock') ?? 0);
                                if ($qty > $available) {
                                    $set('__stock_error', "Stock insufficient! Available: {$available}");
                                } else {
                                    $set('__stock_error', null);
                                }
                            })
                            ->columnSpan(1),
                        TextInput::make('price')
                            ->label('Price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $price = floatval($state ?? 0);
                                $qty = intval($get('quantity') ?? 1);
                                $set('subtotal', round($price * $qty, 2));
                            })
                            ->columnSpan(1),
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->readOnly()
                            ->columnSpan(1),
                        // Hidden field for available stock
                        Hidden::make('available_stock')->default(0),
                        // Hidden field for stock error message
                        Hidden::make('__stock_error')->default(null),
                    ])
                    ->columns(5)
                    ->columnSpanFull(),

                Hidden::make('subtotal')->default(0),
                Hidden::make('tax')->default(0),
                Hidden::make('total')->default(0),
            ]);
    }
}
