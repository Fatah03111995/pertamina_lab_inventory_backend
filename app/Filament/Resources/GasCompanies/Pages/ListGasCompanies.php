<?php

namespace App\Filament\Resources\GasCompanies\Pages;

use App\Filament\Resources\GasCompanies\GasCompanyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListGasCompanies extends ListRecords
{
    protected static string $resource = GasCompanyResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Daftar Gas Cylinder Owner';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Gas Cylinder Owner Baru'),
        ];
    }
}
