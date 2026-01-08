<?php

namespace App\Filament\Resources\GasCompanies;

use App\Filament\Resources\GasCompanies\Pages\CreateGasCompany;
use App\Filament\Resources\GasCompanies\Pages\EditGasCompany;
use App\Filament\Resources\GasCompanies\Pages\ListGasCompanies;
use App\Filament\Resources\GasCompanies\Pages\ViewGasCompany;
use App\Filament\Resources\GasCompanies\Schemas\GasCompanyForm;
use App\Filament\Resources\GasCompanies\Schemas\GasCompanyInfolist;
use App\Filament\Resources\GasCompanies\Tables\GasCompaniesTable;
use App\Models\GasCompany;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class GasCompanyResource extends Resource
{
    protected static ?string $model = GasCompany::class;

    public static ?string $navigationLabel = 'Gas Cylinder Owner';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Database';
    }

    protected static string|BackedEnum|null $navigationIcon = 'my-gas_owner';

    public static function form(Schema $schema): Schema
    {
        return GasCompanyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GasCompanyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GasCompaniesTable::configure($table);
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
            'index' => ListGasCompanies::route('/'),
            'create' => CreateGasCompany::route('/create'),
            'view' => ViewGasCompany::route('/{record}'),
            'edit' => EditGasCompany::route('/{record}/edit'),
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
