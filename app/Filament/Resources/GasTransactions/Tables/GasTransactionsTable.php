<?php

namespace App\Filament\Resources\GasTransactions\Tables;

use App\Enums\GasEventType;
use App\Models\GasCompany;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class GasTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_number')
                    ->label('No Dokumen')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('event_type')
                    ->label('Tipe Movement')
                    ->badge()
                    ->color(function ($state) {
                        match($state) {
                            GasEventType::MAINTENANCE_START => 'warning',
                            GasEventType::MAINTENANCE_END => 'warning',
                            GasEventType::TAKE_FOR_REFILL => 'info',
                            GasEventType::RETURN_FROM_REFILL => 'info',
                            GasEventType::REPORT_LOST => 'danger',
                            GasEventType::RESOLVE_ISSUE => 'danger',
                            GasEventType::MOVEMENT_EXTERNAL => 'success',
                            GasEventType::MOVEMENT_INTERNAL => 'success',
                            default => 'secondary'
                        };
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label('Perusahaan')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('event_type')
                    ->label('Tipe Movement')
                    ->options(
                        GasEventType::labels()
                    )
                    ->placeholder('Semua'),
                SelectFilter::make('company')
                    ->label('Perusahaan')
                    ->options(
                        GasCompany::query()->pluck('name', 'id')
                    )
                    ->placeholder('Semua')
                    ], FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
