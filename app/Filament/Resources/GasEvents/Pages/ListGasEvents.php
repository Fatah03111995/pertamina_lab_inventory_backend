<?php

namespace App\Filament\Resources\GasEvents\Pages;

use App\Filament\Resources\GasEvents\GasEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGasEvents extends ListRecords
{
    protected static string $resource = GasEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
