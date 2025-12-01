<?php

namespace App\Filament\Resources\GasCompanies\Tables;

use App\Models\GasCompany;
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

class GasCompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge(),
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
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(
                        GasCompany::query()->pluck('name', 'id')
                    )
                    ->placeholder('Semua')
                    ], FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()->action(function ($record) {
                    try {
                        $record->delete();
                        \Filament\Notifications\Notification::make()->success()->title('Berhasil')->body('Perusahaan dihapus')->send();
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
                        \Filament\Notifications\Notification::make()->success()->title('Berhasil')->body('Beberapa perusahaan dihapus')->send();
                    }),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
