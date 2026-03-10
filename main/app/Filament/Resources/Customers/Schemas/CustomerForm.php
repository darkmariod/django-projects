<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('identification')
                    ->required()
                    ->maxLength(50),
                Select::make('identification_type')
                    ->required()
                    ->options([
                        'cedula' => 'Cédula',
                        'ruc' => 'RUC',
                        'passport' => 'Pasaporte',
                    ]),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(50)
                    ->default(null),
                TextInput::make('address')
                    ->maxLength(500)
                    ->default(null),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
