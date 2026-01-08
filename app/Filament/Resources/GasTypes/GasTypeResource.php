<?php

namespace App\Filament\Resources\GasTypes;

use App\Filament\Resources\GasTypes\Pages\CreateGasType;
use App\Filament\Resources\GasTypes\Pages\EditGasType;
use App\Filament\Resources\GasTypes\Pages\ListGasTypes;
use App\Filament\Resources\GasTypes\Pages\ViewGasType;
use App\Filament\Resources\GasTypes\Schemas\GasTypeForm;
use App\Filament\Resources\GasTypes\Schemas\GasTypeInfolist;
use App\Filament\Resources\GasTypes\Tables\GasTypesTable;
use App\Models\GasType;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GasTypeResource extends Resource
{
    protected static ?string $model = GasType::class;

    public static ?string $navigationLabel = 'Tipe Gas';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Database';
    }

    protected static string|BackedEnum|null $navigationIcon = 'my-category';

    public static function form(Schema $schema): Schema
    {
        return GasTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GasTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GasTypesTable::configure($table);
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
            'index' => ListGasTypes::route('/'),
            'create' => CreateGasType::route('/create'),
            'view' => ViewGasType::route('/{record}'),
            'edit' => EditGasType::route('/{record}/edit'),
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
