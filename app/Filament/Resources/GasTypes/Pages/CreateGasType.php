<?php

namespace App\Filament\Resources\GasTypes\Pages;

use App\Filament\Resources\GasTypes\GasTypeResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateGasType extends CreateRecord
{
    protected static string $resource = GasTypeResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Tipe Gas Baru';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
