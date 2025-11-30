<?php

namespace App\Filament\Resources\GasCylinders\Tables;

use App\Enums\GasCylinderStatus;
use App\Models\GasLocation;
use App\Models\GasType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class GasCylindersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('gasType.name')
                    ->label('Tipe Gas')
                    ->sortable(),
                TextColumn::make('serial_number')
                    ->searchable(),
                TextColumn::make('vendor_code')
                    ->label('Kode Vendor')
                    ->searchable()
                    ->default('-'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        GasCylinderStatus::EMPTY => 'danger',
                        GasCylinderStatus::FILLED => 'success',
                        GasCylinderStatus::IN_USE => 'info',
                        GasCylinderStatus::MAINTENANCE => 'warning',
                        GasCylinderStatus::LOST => 'gray',
                        GasCylinderStatus::REFILL_PROCESS => 'primary',
                        default => 'secondary',
                    }),
                TextColumn::make('currentLocation.name')
                    ->label('Lokasi')
                    ->searchable(),
                TextColumn::make('companyOwner.name')
                    ->label('Pemilik')
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
                SelectFilter::make('Tipe Gas')
                    ->options(
                        GasType::query()->pluck('name', 'id')
                    )
                    ->placeholder('Semua'),
                SelectFilter::make('Lokasi')
                    ->options(
                        GasLocation::query()->pluck('name', 'id')
                    )
                    ->placeholder('Semua'),
                SelectFilter::make('Pemilik')
                    ->options(
                        GasLocation::query()->pluck('name', 'id')
                    )
                    ->placeholder('Semua'),
                SelectFilter::make('Status')
                    ->options(
                        GasCylinderStatus::labels()
                    )
                    ->placeholder('Semua')
                    ->multiple()
                ], FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()->action(function ($record) {
                    try {
                        $record->delete();
                        \Filament\Notifications\Notification::make()->success()->title('Berhasil')->body('Gas cylinder dihapus')->send();
                    } catch (\App\Exceptions\InvariantViolationException $e) {
                        \Filament\Notifications\Notification::make()->danger()->title('Gagal')->body($e->getMessage())->send();
                    }
                }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->action(function ($records) {
                        foreach ($records as $record) {
                            try {
                                $record->delete();
                            } catch (\App\Exceptions\InvariantViolationException $e) {
                                \Filament\Notifications\Notification::make()->danger()->title('Gagal')->body($e->getMessage())->send();
                                return;
                            }
                        }
                        \Filament\Notifications\Notification::make()->success()->title('Berhasil')->body('Beberapa gas cylinder dihapus')->send();
                    }),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
