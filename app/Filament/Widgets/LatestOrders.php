<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = 'Latest Orders';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with('user')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('id')
                    ->label('Order #')
                    ->formatStateUsing(fn($state) => '#' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->icon('heroicon-m-user'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'shipping' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
