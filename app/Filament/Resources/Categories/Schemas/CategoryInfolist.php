<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Category Details ─────────────────────────────────────────
                Section::make('Category Details')
                    ->icon('heroicon-o-squares-2x2')
                    ->columns(2)
                    ->components([
                        TextEntry::make('name')
                            ->label('Category Name')
                            ->weight(FontWeight::Bold)
                            ->color('primary'),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->badge()
                            ->color('gray'),
                    ]),

                // ── System Timestamps ──────────────────────────────────────────
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
