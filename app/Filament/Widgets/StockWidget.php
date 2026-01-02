<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\GasType;
use App\Models\GasCylinder;
use App\Enums\GasLocationCategory;
use App\Enums\GasCylinderStatus;
use Dom\Text;

class StockWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(GasType::query()->withCount(["gasCylinders as filled_in_storage_count" => function (Builder $query) {
                $query->where('status', GasCylinderStatus::FILLED->value)
                      ->whereHas('currentLocation', function ($q) {
                          $q->where('category', GasLocationCategory::STORAGE->value);
                      });
            }]))
            ->columns([
                TextColumn::make('name')->label('Gas Type')->searchable()->sortable(),
                TextColumn::make('min_stock')->label('Min Stock')->numeric()->sortable(),
                TextColumn::make('filled_in_storage_count')
                    ->label('Filled in Storage')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->colors([
                        'danger' => fn ($state, $record): bool => $state < ($record->min_stock ?? 0),
                        'primary' => fn ($state, $record): bool => $state >= ($record->min_stock ?? 0),
                    ]),
            ]);
    }
}
