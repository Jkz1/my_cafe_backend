<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Str;

class CouponsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->required()->suffixAction(
                        Action::make('generateCode')
                            ->icon('heroicon-m-arrow-path')
                            ->tooltip('Generate random code')
                            ->action(function (Set $set) {
                                $set('code', str(Str::random(10))->upper()->toString());
                            })
                    ),
                Select::make('type')
                    ->options(['fixed' => 'Fixed', 'percent' => 'Percent'])
                    ->default('fixed')
                    ->required(),
                TextInput::make('value')
                    ->required()
                    ->numeric(),
                TextInput::make('min_spend')
                    ->numeric(),
                TextInput::make('usage_limit')
                    ->numeric(),
                TextInput::make('used_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('user_limit')
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('expires_at')
                    ->required(),
            ]);
    }
}
