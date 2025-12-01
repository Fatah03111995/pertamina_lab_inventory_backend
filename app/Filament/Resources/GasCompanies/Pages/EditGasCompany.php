<?php

namespace App\Filament\Resources\GasCompanies\Pages;

use App\Filament\Resources\GasCompanies\GasCompanyResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditGasCompany extends EditRecord
{
    protected static string $resource = GasCompanyResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Edit ' . $this->record->name ;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }
}
