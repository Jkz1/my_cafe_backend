<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('total_price')
                    ->money(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('coupon_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('discount_amount')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('subtotal')
                    ->numeric(),
            ]);
    }
}
