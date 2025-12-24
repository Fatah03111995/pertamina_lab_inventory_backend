<?php

namespace App\Filament\Resources\GasEvents;

use App\Filament\Resources\GasEvents\Pages\CreateGasEvent;
use App\Filament\Resources\GasEvents\Pages\EditGasEvent;
use App\Filament\Resources\GasEvents\Pages\ListGasEvents;
use App\Filament\Resources\GasEvents\Pages\ViewGasEvent;
use App\Filament\Resources\GasEvents\Schemas\GasEventForm;
use App\Filament\Resources\GasEvents\Schemas\GasEventInfolist;
use App\Filament\Resources\GasEvents\Tables\GasEventsTable;
use App\Models\GasEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GasEventResource extends Resource
{
    protected static ?string $model = GasEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return GasEventForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GasEventInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GasEventsTable::configure($table);
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
            'index' => ListGasEvents::route('/'),
            'create' => CreateGasEvent::route('/create'),
            'view' => ViewGasEvent::route('/{record}'),
            'edit' => EditGasEvent::route('/{record}/edit'),
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
