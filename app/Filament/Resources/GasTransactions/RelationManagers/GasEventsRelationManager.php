<?php

namespace App\Filament\Resources\GasTransactions\RelationManagers;

use App\Models\GasEvent;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\GasTransactions\Pages\ViewGasTransaction;

class GasEventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    protected static ?string $recordTitleAttribute = 'id';

    protected static ?string $title = 'List Tabung';


    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $pageClass === ViewGasTransaction::class;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gasCylinder.name')
                    ->label('Tabung'),

                TextColumn::make('toLocation.name')
                    ->label('Lokasi Saat Ini')
                    ->placeholder('-'),

                TextColumn::make('to_status')
                    ->label('Status Saat Ini')
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc');
    }

}
