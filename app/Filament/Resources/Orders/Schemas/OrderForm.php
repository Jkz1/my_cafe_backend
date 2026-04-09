<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('total_price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'shipping' => 'Shipping',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ])
                    ->default('pending')
                    ->required(),
                TextInput::make('coupon_id')
                    ->numeric(),
                TextInput::make('discount_amount')
                    ->numeric()
                    ->default(0.0),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->default(0.0),
            ]);
    }
}
