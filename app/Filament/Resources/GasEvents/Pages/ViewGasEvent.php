<?php

namespace App\Filament\Resources\GasEvents\Pages;

use App\Filament\Resources\GasEvents\GasEventResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGasEvent extends ViewRecord
{
    protected static string $resource = GasEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
