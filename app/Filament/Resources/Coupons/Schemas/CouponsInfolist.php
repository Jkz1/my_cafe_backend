<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Models\Coupons;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class CouponsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Coupon Details ──────────────────────────────────────────
                Section::make('Coupon Details')
                    ->icon('heroicon-o-ticket')
                    ->columns(3)
                    ->components([
                        TextEntry::make('name')
                            ->label('Coupon Name')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('code')
                            ->label('Coupon Code')
                            ->badge()
                            ->color('warning'),

                        IconEntry::make('is_active')
                            ->label('Status')
                            ->boolean(),
                    ]),

                // ── Value & Config ─────────────────────────────────────────
                Section::make('Value & Rules')
                    ->icon('heroicon-o-banknotes')
                    ->columns(2)
                    ->components([
                        TextEntry::make('type')
                            ->label('Discount Type')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'fixed' => 'success',
                                'percent' => 'info',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn($state) => ucfirst($state)),

                        TextEntry::make('value')
                            ->label('Discount Value')
                            ->weight(FontWeight::Bold)
                            ->formatStateUsing(fn($record, $state) => $record->type === 'percent' ? $state . '%' : '$' . number_format($state, 2)),
                    ]),

                // ── Usage limits ───────────────────────────────────────────
                Section::make('Usage Limits & Tracking')
                    ->icon('heroicon-o-chart-bar')
                    ->columns(4)
                    ->components([
                        TextEntry::make('min_spend')
                            ->label('Min Spend')
                            ->money('USD')
                            ->placeholder('—'),

                        TextEntry::make('usage_limit')
                            ->label('Total Limit')
                            ->numeric()
                            ->placeholder('Unlimited'),

                        TextEntry::make('used_count')
                            ->label('Times Used')
                            ->numeric()
                            ->weight(FontWeight::Bold),

                        TextEntry::make('user_limit')
                            ->label('Limit Per User')
                            ->numeric()
                            ->placeholder('Unlimited'),
                    ]),

                // ── Validity ────────────────────────────────────────────────
                Section::make('Validity Schedule')
                    ->icon('heroicon-o-calendar')
                    ->columns(2)
                    ->components([
                        TextEntry::make('starts_at')
                            ->label('Starts At')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('—'),

                        TextEntry::make('expires_at')
                            ->label('Expires At')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('—'),
                    ]),

                // ── Timestamps ──────────────────────────────────────────────
                Section::make('System Timestamps')
                    ->icon('heroicon-o-clock')
                    ->columns(3)
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

                        TextEntry::make('deleted_at')
                            ->label('Deleted At')
                            ->dateTime('d M Y, H:i:s')
                            ->placeholder('—')
                            ->visible(fn(Coupons $record): bool => $record->trashed()),
                    ]),
            ]);
    }
}
