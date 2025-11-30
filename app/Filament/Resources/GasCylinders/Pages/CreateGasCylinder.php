<?php

namespace App\Filament\Resources\GasCylinders\Pages;

use App\Filament\Resources\GasCylinders\GasCylinderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateGasCylinder extends CreateRecord
{
    protected static string $resource = GasCylinderResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Gas Cylinder Baru';
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
