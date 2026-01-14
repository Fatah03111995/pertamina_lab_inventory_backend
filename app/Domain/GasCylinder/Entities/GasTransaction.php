<?php

namespace App\Domain\GasCylinder\Entities;

use App\Enums\GasCylinderStatus;
use App\Enums\GasTransactionType;

/**
 * GasTransaction Domain Entity
 * Represents a single transaction/event of a gas cylinder in the domain layer
 */
class GasTransaction
{
    public string $id;
    public string $gasCylinderId;
    public ?string $headerId;
    public GasTransactionType $transactionType;
    public ?string $fromLocationId;
    public ?string $toLocationId;
    public ?GasCylinderStatus $fromStatus;
    public ?GasCylinderStatus $toStatus;
    public string $notes;
    public string $createdBy;
    public array $metadata;
    public ?\DateTime $createdAt;
    public ?\DateTime $updatedAt;

    /**
     * Create a new GasTransaction entity from a Model instance
     */
    public static function fromModel(\App\Models\GasTransaction $model): self
    {
        $entity = new self();
        $entity->id = $model->id;
        $entity->gasCylinderId = $model->gas_cylinder_id;
        $entity->headerId = $model->header_id;
        $entity->transactionType = $model->transaction_type;
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

    public function hasLocationChange(): bool
    {
        return $this->fromLocationId !== $this->toLocationId;
    }

    public function hasStatusChange(): bool
    {
        return $this->fromStatus !== $this->toStatus;
    }

    public function isRefill(): bool
    {
        return in_array($this->transactionType, [
            GasTransactionType::TAKE_FOR_REFILL,
            GasTransactionType::RETURN_FROM_REFILL,
        ]);
    }

    public function isMovement(): bool
    {
        return $this->transactionType === GasTransactionType::MOVEMENT;
    }

    public function isUsage(): bool
    {
        return $this->transactionType === GasTransactionType::MARK_EMPTY;
    }

    public function isMaintenance(): bool
    {
        return in_array($this->transactionType, [
            GasTransactionType::TAKE_FOR_MAINTENANCE,
            GasTransactionType::RETURN_FROM_MAINTENANCE,
        ]);
    }

    public function getSummary(): string
    {
        $summary = "{$this->transactionType->value}: ";
        $summary .= ($this->fromStatus?->value ?? 'N/A') . " → " . ($this->toStatus?->value ?? 'N/A');

        if ($this->hasLocationChange()) {
            $summary .= " (Location changed)";
        }

        return $summary;
    }

    public function addMetadata(string $key, mixed $value): void
    {
        $this->metadata[$key] = $value;
    }

    public function getMetadata(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }
}
