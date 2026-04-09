<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Models\Coupons;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CouponsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('code'),
                TextEntry::make('type')
                    ->badge(),
                TextEntry::make('value')
                    ->numeric(),
                TextEntry::make('min_spend')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('usage_limit')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('used_count')
                    ->numeric(),
                TextEntry::make('user_limit')
                    ->numeric()
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('starts_at')
                    ->dateTime(),
                TextEntry::make('expires_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Coupons $record): bool => $record->trashed()),
            ]);
    }
}
