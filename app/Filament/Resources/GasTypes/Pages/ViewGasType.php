<?php

namespace App\Filament\Resources\GasTypes\Pages;

use App\Filament\Resources\GasTypes\GasTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewGasType extends ViewRecord
{
    protected static string $resource = GasTypeResource::class;

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
