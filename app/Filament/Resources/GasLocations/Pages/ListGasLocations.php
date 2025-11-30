<?php

namespace App\Filament\Resources\GasLocations\Pages;

use App\Filament\Resources\GasLocations\GasLocationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListGasLocations extends ListRecords
{
    protected static string $resource = GasLocationResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Daftar Lokasi Gas Cylinder';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Lokasi Gas Cylinder Baru'),
        ];
    }
}
