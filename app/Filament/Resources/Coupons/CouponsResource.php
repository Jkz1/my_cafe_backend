<?php

namespace App\Filament\Resources\Coupons;

use App\Filament\Resources\Coupons\Pages\CreateCoupons;
use App\Filament\Resources\Coupons\Pages\EditCoupons;
use App\Filament\Resources\Coupons\Pages\ListCoupons;
use App\Filament\Resources\Coupons\Pages\ViewCoupons;
use App\Filament\Resources\Coupons\Schemas\CouponsForm;
use App\Filament\Resources\Coupons\Schemas\CouponsInfolist;
use App\Filament\Resources\Coupons\Tables\CouponsTable;
use App\Models\Coupons;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CouponsResource extends Resource
{
    protected static ?string $model = Coupons::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Coupons';

    public static function form(Schema $schema): Schema
    {
        return CouponsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CouponsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CouponsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupons::route('/create'),
            'view' => ViewCoupons::route('/{record}'),
            'edit' => EditCoupons::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
