<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('identification'),
                TextEntry::make('identification_type')
                    ->label('ID Type')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextEntry::make('email')
                    ->label('Email'),
                TextEntry::make('phone'),
                TextEntry::make('address'),
                TextEntry::make('is_active')
                    ->label('Active')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
