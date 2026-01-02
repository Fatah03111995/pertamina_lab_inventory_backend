<?php

namespace App\Filament\Resources\GasTransactions;

use App\Filament\Resources\GasTransactions\Pages\CreateGasTransaction;
use App\Filament\Resources\GasTransactions\Pages\EditGasTransaction;
use App\Filament\Resources\GasTransactions\Pages\ListGasTransactions;
use App\Filament\Resources\GasTransactions\Pages\ViewGasTransaction;
use App\Filament\Resources\GasTransactions\Schemas\GasTransactionForm;
use App\Filament\Resources\GasTransactions\Schemas\GasTransactionInfolist;
use App\Filament\Resources\GasTransactions\Tables\GasTransactionsTable;
use App\Models\GasTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GasTransactions\RelationManagers\GasEventsRelationManager;
use UnitEnum;

class GasTransactionResource extends Resource
{
    protected static ?string $model = GasTransaction::class;

    public static ?string $navigationLabel = 'Transaksi Pergerakan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return GasTransactionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GasTransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GasTransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            GasEventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGasTransactions::route('/'),
            'create' => CreateGasTransaction::route('/create'),
            'view' => ViewGasTransaction::route('/{record}'),
            'edit' => EditGasTransaction::route('/{record}/edit'),
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
