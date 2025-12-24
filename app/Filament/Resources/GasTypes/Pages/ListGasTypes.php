<?php

namespace App\Filament\Resources\GasTypes\Pages;

use App\Filament\Resources\GasTypes\GasTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListGasTypes extends ListRecords
{
    protected static string $resource = GasTypeResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Daftar Tipe Gas';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tipe Gas Baru'),
        ];
    }
}
