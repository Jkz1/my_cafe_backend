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
use \Filament\Actions\BulkAction;
use \Filament\Forms\Components\Select;
use \Illuminate\Database\Eloquent\Collection;
use \Filament\Notifications\Notification;
use \App\Models\Coupons;
use \App\Services\OrderService;
use \App\Services\CouponsService;
use Carbon\Carbon;
class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $filters = request()->input('tableFilters.order_date', []);

                return app(OrderService::class)->userOrderStats(
                    $query,
                    data_get($filters, 'order_from'),
                    data_get($filters, 'order_until')
                );
            })
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
                TextColumn::make('total_orders_count')
                    ->label('Total Orders')
                    ->sortable(),
                TextColumn::make('total_spend_sum')
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
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if (!empty($data['order_from']) && !empty($data['order_until'])) {
                            $indicators[] = '📅 Orders from '
                                . Carbon::parse($data['order_from'])->toFormattedDateString()
                                . ' to '
                                . Carbon::parse($data['order_until'])->toFormattedDateString();
                        } elseif (!empty($data['order_from'])) {
                            $indicators[] = '📅 Orders from ' . Carbon::parse($data['order_from'])->toFormattedDateString();
                        } elseif (!empty($data['order_until'])) {
                            $indicators[] = '📅 Orders until ' . Carbon::parse($data['order_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('assign_coupon')
                        ->label('Assign Coupon')
                        ->icon('heroicon-o-ticket')
                        ->form([
                            Select::make('coupon_id')
                                ->label('Select Coupon')
                                ->options(Coupons::where('is_active', true)->pluck('name', 'id'))
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $coupon = Coupons::find($data['coupon_id']);
                            if ($coupon) {
                                app(CouponsService::class)->assignToUsers($coupon, $records);

                                Notification::make()
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
