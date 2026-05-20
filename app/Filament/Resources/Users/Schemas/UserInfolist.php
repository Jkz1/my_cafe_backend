<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Personal Info ──────────────────────────────────────────
                Section::make('Personal Information')
                    ->icon('heroicon-o-user')
                    ->columns(3)
                    ->components([
                        TextEntry::make('name')
                            ->label('Name')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-o-envelope'),

                        TextEntry::make('email_verified_at')
                            ->label('Verification Status')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('Unverified')
                            ->color(fn($state) => $state ? 'success' : 'danger')
                            ->weight(FontWeight::Bold),
                    ]),

                // ── Shopping Statistics ─────────────────────────────────────
                Section::make('Shopping Statistics')
                    ->icon('heroicon-o-shopping-bag')
                    ->columns(2)
                    ->components([
                        TextEntry::make('orders_count')
                            ->label('Total Orders Placed')
                            ->state(fn($record) => $record->orders()->count())
                            ->weight(FontWeight::Bold)
                            ->badge()
                            ->color('info'),

                        TextEntry::make('total_spend')
                            ->label('Total Spend')
                            ->money('USD')
                            ->state(fn($record) => $record->orders()->sum('total_price'))
                            ->weight(FontWeight::Bold)
                            ->color('success'),
                    ]),

                // ── System Timestamps ──────────────────────────────────────
                Section::make('System Timestamps')
                    ->icon('heroicon-o-clock')
                    ->columns(2)
                    ->collapsed()
                    ->components([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d M Y, H:i:s')
                            ->placeholder('—'),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('d M Y, H:i:s')
                            ->placeholder('—'),
                    ]),
            ]);
    }
}
