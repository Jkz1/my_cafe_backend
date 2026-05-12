<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Order Summary ──────────────────────────────────────────
                Section::make('Order Summary')
                    ->icon('heroicon-o-shopping-cart')
                    ->columns(3)
                    ->components([
                        TextEntry::make('id')
                            ->label('Order ID')
                            ->prefix('#')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'pending' => 'warning',
                                'shipping' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('created_at')
                            ->label('Ordered At')
                            ->dateTime('d M Y, H:i')
                            ->placeholder('—'),
                    ]),

                // ── Customer Information ───────────────────────────────────
                Section::make('Customer Information')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->components([
                        TextEntry::make('user.name')
                            ->label('Name'),

                        TextEntry::make('user.email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope'),
                    ]),

                // ── Payment Breakdown ──────────────────────────────────────
                Section::make('Payment Breakdown')
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->components([
                        TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->money('USD')
                            ->placeholder('—'),

                        TextEntry::make('discount_amount')
                            ->label('Discount')
                            ->money('USD')
                            ->placeholder('—')
                            ->color('danger'),

                        TextEntry::make('total_price')
                            ->label('Total Paid')
                            ->money('USD')
                            ->weight(FontWeight::Bold)
                            ->color('success'),
                    ]),

                // ── Coupon Information ─────────────────────────────────────
                Section::make('Coupon')
                    ->icon('heroicon-o-ticket')
                    ->columns(3)
                    ->collapsed()
                    ->components([
                        TextEntry::make('coupon.code')
                            ->label('Coupon Code')
                            ->placeholder('No coupon used')
                            ->badge()
                            ->color('warning'),

                        TextEntry::make('coupon.type')
                            ->label('Discount Type')
                            ->placeholder('—')
                            ->formatStateUsing(fn($state) => $state ? ucfirst($state) : '—'),

                        TextEntry::make('coupon.value')
                            ->label('Discount Value')
                            ->placeholder('—'),
                    ]),

                // ── Order Items ────────────────────────────────────────────
                Section::make('Order Items')
                    ->icon('heroicon-o-list-bullet')
                    ->components([
                        RepeatableEntry::make('details')
                            ->label('')
                            ->columns(4)
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Product'),

                                TextEntry::make('unit_price')
                                    ->label('Unit Price')
                                    ->money('USD'),

                                TextEntry::make('quantity')
                                    ->label('Qty')
                                    ->numeric(),

                                TextEntry::make('line_total')
                                    ->label('Line Total')
                                    ->money('USD')
                                    ->state(fn($record) => $record->unit_price * $record->quantity)
                                    ->weight(FontWeight::Bold),
                            ]),
                    ]),

                // ── Timestamps ────────────────────────────────────────────
                Section::make('Timestamps')
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
