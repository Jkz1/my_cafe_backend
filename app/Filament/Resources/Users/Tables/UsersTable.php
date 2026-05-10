<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Total Orders')
                    ->sortable(),
                TextColumn::make('orders_sum_total_price')
                    ->sum('orders', 'total_price')
                    ->label('Total Spend')
                    ->money()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('order_date')
                    ->form([
                        DatePicker::make('order_from')->label('Order From'),
                        DatePicker::make('order_until')->label('Order Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['order_from'],
                                fn(Builder $query, $date): Builder => $query->whereHas('orders', fn(Builder $query) => $query->whereDate('created_at', '>=', $date)),
                            )
                            ->when(
                                $data['order_until'],
                                fn(Builder $query, $date): Builder => $query->whereHas('orders', fn(Builder $query) => $query->whereDate('created_at', '<=', $date)),
                            );
                    })
            ])
            ->recordActions([
                ViewAction::make(),
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    \Filament\Actions\BulkAction::make('assign_coupon')
                        ->label('Assign Coupon')
                        ->icon('heroicon-o-ticket')
                        ->form([
                            \Filament\Forms\Components\Select::make('coupon_id')
                                ->label('Select Coupon')
                                ->options(\App\Models\Coupons::where('is_active', true)->pluck('name', 'id'))
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            $coupon = \App\Models\Coupons::find($data['coupon_id']);
                            if ($coupon) {
                                foreach ($records as $user) {
                                    $user->coupons()->syncWithoutDetaching([$coupon->id]);
                                }
                                \Filament\Notifications\Notification::make()
                                    ->title('Coupon assigned successfully')
                                    ->success()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
