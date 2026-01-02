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
use App\Models\GasTransaction as GasTransactionModel;
use App\Enums\GasEventType;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use App\Domain\GasCylinder\Services\Validation\GasTransactionAssertions;

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
        $notes = $data['notes'] ?? '';
        $documentNumber = $data['document_number'] ?? '';
        $evidenceDocument = $data['evidence_document'] ?? '';
        $eventHandler = new GasCylinderHandler();
        $cylinderIds = Arr::wrap($data['gas_cylinder_id'] ?? []);
        $cylinders = GasCylinder::whereIn('id', $cylinderIds)->get()->all();
        $toLocationId = $data['to_location_id'] ?? null;
        $toLocation = $toLocationId === null ? null : GasLocation::find($toLocationId);
        $toStatus = $data['toStatus'] ?? '';
        $metadata = [];

        $transaction = null;
        $events = [];
        // Assertions for transaction
        try {
            $txAssertions = new GasTransactionAssertions();
            $txAssertions->assertGeneral(
                \App\Enums\GasEventType::from($eventType),
                $documentNumber,
                is_array($evidenceDocument) ? ($evidenceDocument['0'] ?? '') : $evidenceDocument,
                $notes,
                $user
            );
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Validasi transaksi gagal: ' . $e->getMessage())
                ->danger()
                ->send();
            throw $e;
        }

        try {
            $result = DB::transaction(function () use (
                $transactionId,
                $eventType,
                $documentNumber,
                $evidenceDocument,
                $notes,
                $user,
                $eventHandler,
                $cylinders,
                $toLocation,
                $toStatus,
                $metadata,
            ) {
                $tx = new GasTransactionModel();
                $tx->id = $transactionId;
                $tx->event_type = $eventType;
                $tx->document_number = $documentNumber;
                $tx->evidence_document = is_array($evidenceDocument) ? ($evidenceDocument[0] ?? null) : $evidenceDocument;
                $tx->notes = $notes;
                $tx->created_by = $user->id;
                $tx->save();

                if ($eventType === GasEventType::MOVEMENT_INTERNAL->value) {
                    $events = $eventHandler->useCylinders($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
                } elseif ($eventType === GasEventType::MOVEMENT_EXTERNAL->value) {
                    $events = $eventHandler->externalMovementMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
                } elseif ($eventType === GasEventType::MARK_EMPTY->value) {
                    $events = $eventHandler->markEmptyMultiple($cylinders, $user, $metadata, $notes, $transactionId);
                } elseif ($eventType === GasEventType::TAKE_FOR_REFILL->value) {
                    $events = $eventHandler->takeForRefillMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
                } elseif ($eventType === GasEventType::RETURN_FROM_REFILL->value) {
                    $events = $eventHandler->returnFromRefillMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
                } elseif ($eventType === GasEventType::MAINTENANCE_START->value) {
                    $events = $eventHandler->startMaintenanceMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
                } elseif ($eventType === GasEventType::MAINTENANCE_END->value) {
                    $events = $eventHandler->endMaintenanceMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
                } elseif ($eventType === GasEventType::RESOLVE_ISSUE->value) {
                    $events = $eventHandler->resolveIssueMultiple($cylinders, $toLocation, $toStatus, $user, $notes, $metadata, $transactionId);
                } elseif ($eventType === GasEventType::REPORT_LOST->value) {
                    $events = $eventHandler->reportLostMultiple($cylinders, $user, $notes, $metadata, $transactionId);
                } else {
                    throw new \Exception('Unsupported event type');
                }

                return ['tx' => $tx, 'events' => $events];
            });

            $transaction = $result['tx'];
            $events = $result['events'] ?? [];

            // If user selected cylinders but no events were created, rollback
            if (count($cylinders) > 0 && count($events) === 0) {
                throw new \Exception('Tidak ada event dibuat untuk tabung yang dipilih');
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Gagal memproses transaksi/event: ' . $e->getMessage())
                ->danger()
                ->send();
            throw $e;
        }
        return $transaction;
    }
}
