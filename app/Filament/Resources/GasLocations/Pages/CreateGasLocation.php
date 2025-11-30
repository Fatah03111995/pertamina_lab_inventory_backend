<?php

namespace App\Filament\Resources\GasLocations\Pages;

use App\Filament\Resources\GasLocations\GasLocationResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateGasLocation extends CreateRecord
{
    protected static string $resource = GasLocationResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Lokasi Gas Cylinder Baru';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl();
    }
}
