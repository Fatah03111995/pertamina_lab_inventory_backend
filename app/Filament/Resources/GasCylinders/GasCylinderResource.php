<?php

namespace App\Filament\Resources\GasCylinders;

use App\Filament\Resources\GasCylinders\Pages\CreateGasCylinder;
use App\Filament\Resources\GasCylinders\Pages\EditGasCylinder;
use App\Filament\Resources\GasCylinders\Pages\ListGasCylinders;
use App\Filament\Resources\GasCylinders\Pages\ViewGasCylinder;
use App\Filament\Resources\GasCylinders\Schemas\GasCylinderForm;
use App\Filament\Resources\GasCylinders\Schemas\GasCylinderInfolist;
use App\Filament\Resources\GasCylinders\Tables\GasCylindersTable;
use App\Models\GasCylinder;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GasCylinderResource extends Resource
{
    protected static ?string $model = GasCylinder::class;

    public static ?string $navigationLabel = 'Gas Cylinder';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return GasCylinderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GasCylinderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GasCylindersTable::configure($table);
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
            'index' => ListGasCylinders::route('/'),
            'create' => CreateGasCylinder::route('/create'),
            'view' => ViewGasCylinder::route('/{record}'),
            'edit' => EditGasCylinder::route('/{record}/edit'),
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
