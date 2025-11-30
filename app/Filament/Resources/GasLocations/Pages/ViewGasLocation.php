<?php

namespace App\Filament\Resources\GasLocations\Pages;

use App\Filament\Resources\GasLocations\GasLocationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewGasLocation extends ViewRecord
{
    protected static string $resource = GasLocationResource::class;

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
