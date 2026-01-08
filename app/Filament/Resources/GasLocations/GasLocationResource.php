<?php

namespace App\Filament\Resources\GasLocations;

use App\Filament\Resources\GasLocations\Pages\CreateGasLocation;
use App\Filament\Resources\GasLocations\Pages\EditGasLocation;
use App\Filament\Resources\GasLocations\Pages\ListGasLocations;
use App\Filament\Resources\GasLocations\Pages\ViewGasLocation;
use App\Filament\Resources\GasLocations\Schemas\GasLocationForm;
use App\Filament\Resources\GasLocations\Schemas\GasLocationInfolist;
use App\Filament\Resources\GasLocations\Tables\GasLocationsTable;
use App\Models\GasLocation;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GasLocationResource extends Resource
{
    protected static ?string $model = GasLocation::class;

    public static ?string $navigationLabel = 'Lokasi';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Database';
    }

    protected static string|BackedEnum|null $navigationIcon = 'my-location';

    public static function form(Schema $schema): Schema
    {
        return GasLocationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GasLocationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GasLocationsTable::configure($table);
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
            'index' => ListGasLocations::route('/'),
            'create' => CreateGasLocation::route('/create'),
            'view' => ViewGasLocation::route('/{record}'),
            'edit' => EditGasLocation::route('/{record}/edit'),
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
