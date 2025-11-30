<?php

namespace App\Filament\Resources\GasCylinders\Pages;

use App\Filament\Resources\GasCylinders\GasCylinderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewGasCylinder extends ViewRecord
{
    protected static string $resource = GasCylinderResource::class;

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
