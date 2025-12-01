<?php

namespace App\Filament\Resources\GasCompanies\Pages;

use App\Filament\Resources\GasCompanies\GasCompanyResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateGasCompany extends CreateRecord
{
    protected static string $resource = GasCompanyResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Gas Cylinder Owner Baru';
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
