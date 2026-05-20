<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Product Core Details ────────────────────────────────────
                Section::make('Product Details')
                    ->icon('heroicon-o-shopping-bag')
                    ->columns(3)
                    ->components([
                        TextEntry::make('name')
                            ->label('Product Name')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('category.name')
                            ->label('Category')
                            ->badge()
                            ->color('warning')
                            ->placeholder('No Category'),
                    ]),

                // ── Status & Image ──────────────────────────────────────────
                Section::make('Status & Preview')
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->components([
                        ImageEntry::make('image_path')
                            ->label('Product Image')
                            ->square()
                            ->height(120),

                        IconEntry::make('is_available')
                            ->label('Availability Status')
                            ->boolean(),
                    ]),

                // ── Pricing & Inventory ─────────────────────────────────────
                Section::make('Pricing & Inventory')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(2)
                    ->components([
                        TextEntry::make('price')
                            ->label('Price')
                            ->money('USD')
                            ->weight(FontWeight::Bold)
                            ->color('success'),

                        TextEntry::make('stock')
                            ->label('Stock Level')
                            ->numeric()
                            ->weight(FontWeight::Bold)
                            ->color(fn(int $state): string => $state > 5 ? 'success' : ($state > 0 ? 'warning' : 'danger')),
                    ]),

                // ── Description ──────────────────────────────────────────────
                Section::make('Product Description')
                    ->icon('heroicon-o-document-text')
                    ->components([
                        TextEntry::make('description')
                            ->label('')
                            ->placeholder('No description provided for this product.')
                            ->markdown(),
                    ]),

                // ── Timestamps ──────────────────────────────────────────────
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
