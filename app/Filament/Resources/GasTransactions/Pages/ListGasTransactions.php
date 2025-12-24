<?php

namespace App\Filament\Resources\GasTransactions\Pages;

use App\Filament\Resources\GasTransactions\GasTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListGasTransactions extends ListRecords
{
    protected static string $resource = GasTransactionResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Daftar Transaksi Pergerakan';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Dokumen Transaksi Baru'),
        ];
    }
}
