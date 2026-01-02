<?php

namespace App\Domain\GasCylinder\Entities;

use App\Enums\GasCylinderStatus;
use App\Enums\GasEventType;

/**
 * GasEvent Domain Entity
 * Represents an event (state change) of a gas cylinder in the domain layer
 */
class GasEvent
{
    public string $id;
    public string $gasCylinderId;
    public ?string $gasTransactionId;
    public GasEventType $eventType;
    public string $fromLocationId;
    public string $toLocationId;
    public GasCylinderStatus $fromStatus;
    public GasCylinderStatus $toStatus;
    public string $notes;
    public string $createdBy;
    public array $metadata;
    public ?\DateTime $createdAt;
    public ?\DateTime $updatedAt;

    /**
     * Create a new GasEvent entity from a Model instance
     */
    public static function fromModel(\App\Models\GasEvent $model): self
    {
        $entity = new self();
        $entity->id = $model->id;
        $entity->gasCylinderId = $model->gas_cylinder_id;
        $entity->gasTransactionId = $model->gas_transaction_id;
        $entity->eventType = $model->event_type;
        $entity->fromLocationId = $model->from_location_id;
        $entity->toLocationId = $model->to_location_id;
        $entity->fromStatus = $model->from_status;
        $entity->toStatus = $model->to_status;
        $entity->notes = $model->notes ?? '';
        $entity->createdBy = $model->created_by;
        $entity->metadata = $model->metadata ?? [];
        $entity->createdAt = $model->created_at;
        $entity->updatedAt = $model->updated_at;

        return $entity;
    }

    /**
     * Check if event involves location change
     */
    public function hasLocationChange(): bool
    {
        return $this->fromLocationId !== $this->toLocationId;
    }

    /**
     * Check if event involves status change
     */
    public function hasStatusChange(): bool
    {
        return $this->fromStatus !== $this->toStatus;
    }

    /**
     * Check if event is associated with a transaction
     */
    public function hasTransaction(): bool
    {
        return !empty($this->gasTransactionId);
    }

    /**
     * Check if event is a refill-related event
     */
    public function isRefillEvent(): bool
    {
        return in_array($this->eventType, [
            GasEventType::TAKE_FOR_REFILL,
            GasEventType::RETURN_FROM_REFILL,
        ]);
    }

    /**
     * Check if event is a movement event
     */
    public function isMovementEvent(): bool
    {
        return $this->eventType === GasEventType::MOVEMENT_INTERNAL;
    }

    /**
     * Check if event is a usage event
     */
    public function isUsageEvent(): bool
    {
        return $this->eventType === GasEventType::MARK_EMPTY;
    }

    /**
     * Check if event is maintenance-related
     */
    public function isMaintenanceEvent(): bool
    {
        return in_array($this->eventType, [
            GasEventType::MAINTENANCE_START,
            GasEventType::MAINTENANCE_END,
        ]);
    }

    /**
     * Get event summary for logging/display
     */
    public function getSummary(): string
    {
        $summary = "{$this->eventType->value}: ";
        $summary .= "{$this->fromStatus->value} → {$this->toStatus->value}";

        if ($this->hasLocationChange()) {
            $summary .= " (Location changed)";
        }

        return $summary;
    }

    /**
     * Add metadata value
     */
    public function addMetadata(string $key, mixed $value): void
    {
        $this->metadata[$key] = $value;
    }

    /**
     * Get metadata value
     */
    public function getMetadata(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }
}
