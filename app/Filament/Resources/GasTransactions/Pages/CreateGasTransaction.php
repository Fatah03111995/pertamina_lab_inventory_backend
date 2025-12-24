<?php

namespace App\Filament\Resources\GasTransactions\Pages;

use App\Domain\GasCylinder\Services\Handlers\GasCylinderHandler;
use App\Filament\Resources\GasTransactions\GasTransactionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Models\GasCylinder;
use App\Models\GasLocation;
use App\Enums\GasEventType;
use Filament\Notifications\Notification;

class CreateGasTransaction extends CreateRecord
{
    protected static string $resource = GasTransactionResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Transaksi Pergerakan Baru';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function handleRecordCreation(array $data): Model
    {
        $transactionId = Str::ulid();
        $eventType = $data['event_type'];
        $user = Auth::user();
        $notes = $data['notes'];

        //Transaction
        $documentNumber = $data['document_number'] ?? '';
        $evidenceDocument = $data['evidence_document'];
        //id
        //document_number,
        //event_types,
        //evidence_document,
        //notes

        //Event
        $eventHandler = new GasCylinderHandler();
        $cylinderIds = Arr::wrap($data['gas_cylinder_id'] ?? []);
        $cylinders = GasCylinder::whereIn('id', $cylinderIds)->get();
        $toLocationId = $data['to_location_id'] ?? null;
        $toLocation = $toLocationId === null ? null : GasLocation::find($toLocationId);
        $toStatus = $data['toStatus'] ?? '';
        $metadata = [];

        try {
            if ($eventType === GasEventType::MOVEMENT_INTERNAL->value) {
                $events = $eventHandler->useCylinders($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
            } elseif ($eventType === GasEventType::MOVEMENT_EXTERNAL) {
                $events = $eventHandler->externalMovementMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
            } elseif ($eventType === GasEventType::MARK_EMPTY) {
                $events = $eventHandler->markEmptyMultiple($cylinders, $user, $metadata, $notes, $transactionId);
            } elseif ($eventType === GasEventType::TAKE_FOR_REFILL->value) {
                $events = $eventHandler->takeForRefillMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
            } elseif ($eventType === GasEventType::RETURN_FROM_REFILL->value) {
                $events = $eventHandler->returnFromRefillMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
            } elseif ($eventType === GasEventType::MAINTENANCE_START) {
                $events = $eventHandler->startMaintenanceMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
            } elseif ($eventType === GasEventType::MAINTENANCE_END) {
                $events = $eventHandler->endMaintenanceMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
            } elseif ($eventType === GasEventType::RESOLVE_ISSUE) {
                $events = $eventHandler->resolveIssueMultiple($cylinders, $toLocation, $toStatus, $user, $notes, $metadata, $transactionId);
            } elseif ($eventType === GasEventType::REPORT_LOST) {
                $events = $eventHandler->reportLostMultiple($cylinders, $user, $notes, $metadata, $transactionId);
            } else {
                throw new \Exception('Unsupported event type');
            }

            Notification::make()
                ->title('Event berhasil diproses')
                ->success()
                ->send();

            // Return the first event for redirect (Filament expects a Model)
            return is_array($events) ? $events[0] : $events;
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Gagal memproses event: ' . $e->getMessage())
                ->danger()
                ->send();
            throw $e;
        }
    }
}
