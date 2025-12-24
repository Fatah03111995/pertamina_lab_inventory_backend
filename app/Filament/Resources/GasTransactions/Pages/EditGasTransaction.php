<?php

namespace App\Filament\Resources\GasTransactions\Pages;

use App\Filament\Resources\GasTransactions\GasTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditGasTransaction extends EditRecord
{
    protected static string $resource = GasTransactionResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Edit ' . $this->record->document_number;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }
}
