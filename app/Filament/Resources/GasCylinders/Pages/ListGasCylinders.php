<?php

namespace App\Filament\Resources\GasCylinders\Pages;

use App\Filament\Resources\GasCylinders\GasCylinderResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GenericModelExports;

class ListGasCylinders extends ListRecords
{
    protected static string $resource = GasCylinderResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Daftar Gas Cylinder';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Gas Cylinder Baru'),
            Action::make('export_excel')
            ->label('Download Excel')
            ->icon('heroicon-o-document-arrow-down')
            ->action(function ($data, $livewire) {
                $baseQuery = $livewire->getFilteredTableQuery();
                $state = $livewire->getTableFilters();

                return Excel::download(
                    new GenericModelExports(
                        query: $baseQuery,
                        table: $livewire->getTable(),
                        state: $state,
                    ),
                    $this->getTitle() . ' - LAB.xlsx'
                );
            })
        ];
    }
}
