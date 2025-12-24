<?php

namespace App\Filament\Resources\GasTransactions\Pages;

use App\Filament\Resources\GasTransactions\GasTransactionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGasTransaction extends ViewRecord
{
    protected static string $resource = GasTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
