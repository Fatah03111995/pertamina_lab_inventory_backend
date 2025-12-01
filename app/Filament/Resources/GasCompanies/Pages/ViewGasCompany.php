<?php

namespace App\Filament\Resources\GasCompanies\Pages;

use App\Filament\Resources\GasCompanies\GasCompanyResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewGasCompany extends ViewRecord
{
    protected static string $resource = GasCompanyResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
