<?php

namespace App\Filament\Resources\GasLocations\Pages;

use App\Filament\Resources\GasLocations\GasLocationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditGasLocation extends EditRecord
{
    protected static string $resource = GasLocationResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Edit ' .$this->record->name;
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

    public function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }
}
