<?php

namespace App\Filament\Resources\GasEvents\Pages;

use App\Domain\GasCylinder\Services\Handlers\GasCylinderHandler;
use App\Models\GasCylinder;
use App\Models\GasLocation;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use App\Filament\Resources\GasEvents\GasEventResource;
use Filament\Resources\Pages\CreateRecord;
use App\Enums\GasEventType;
use Illuminate\Support\Str;

class CreateGasEvent extends CreateRecord
{
    protected static string $resource = GasEventResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $handler = new GasCylinderHandler();
        $user = Auth::user();
        $eventType = $data['event_type'] ?? null;
        $cylinderIds = Arr::wrap($data['gas_cylinder_id'] ?? []);
        $cylinders = GasCylinder::whereIn('id', $cylinderIds)->get();
        $toLocation = isset($data['to_location_id']) ? GasLocation::find($data['to_location_id']) : null;
        $transactionId = (string) Str::ulid();
        $toStatus = $data['to_status'] ?? null;
        $notes = $data['notes'] ?? '';
        $metadata = $data['metadata'] ?? [];

        try {
            if ($eventType === GasEventType::MOVEMENT_INTERNAL->value) {
                $events = $handler->useCylinders($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
            } elseif ($eventType === GasEventType::MOVEMENT_EXTERNAL) {
                $events = $handler->externalMovementMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
            } elseif ($eventType === GasEventType::MARK_EMPTY) {
                $events = $handler->markEmptyMultiple($cylinders, $user, $metadata, $notes, $transactionId);
            } elseif ($eventType === GasEventType::TAKE_FOR_REFILL->value) {
                $events = $handler->takeForRefillMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
            } elseif ($eventType === GasEventType::RETURN_FROM_REFILL->value) {
                $events = $handler->returnFromRefillMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
            } elseif ($eventType === GasEventType::MAINTENANCE_START) {
                $events = $handler->startMaintenanceMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
            } elseif ($eventType === GasEventType::MAINTENANCE_END) {
                $events = $handler->endMaintenanceMultiple($cylinders, $toLocation, $user, $metadata, $notes, $transactionId);
            } elseif ($eventType === GasEventType::RESOLVE_ISSUE) {
                $events = $handler->resolveIssueMultiple($cylinders, $toLocation, $toStatus, $user, $notes, $metadata, $transactionId);
            } elseif ($eventType === GasEventType::REPORT_LOST) {
                $events = $handler->reportLostMultiple($cylinders, $user, $notes, $metadata, $transactionId);
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
    // ...existing code...
}
