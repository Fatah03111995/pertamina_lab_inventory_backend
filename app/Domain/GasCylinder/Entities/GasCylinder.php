<?php

namespace App\Domain\GasCylinder\Entities;

use App\Enums\GasCylinderStatus;

/**
 * GasCylinder Domain Entity
 * Represents a gas cylinder in the domain layer with business logic
 */
class GasCylinder
{
    public string $id;
    public string $name;
    public string $gasTypeId;
    public string $serialNumber;
    public string $vendorCode;
    public GasCylinderStatus $status;
    public string $currentLocationId;
    public string $companyOwnerId;
    public array $metadata;
    public int $version;
    public ?\DateTime $createdAt;
    public ?\DateTime $updatedAt;

    /**
     * Create a new GasCylinder entity from a Model instance
     */
    public static function fromModel(\App\Models\GasCylinder $model): self
    {
        $entity = new self();
        $entity->id = $model->id;
        $entity->name = $model->name;
        $entity->gasTypeId = $model->gas_type_id;
        $entity->serialNumber = $model->serial_number;
        $entity->vendorCode = $model->vendor_code;
        $entity->status = $model->status;
        $entity->currentLocationId = $model->current_location_id;
        $entity->companyOwnerId = $model->company_owner_id;
        $entity->metadata = $model->metadata ?? [];
        $entity->version = $model->version ?? 1;
        $entity->createdAt = $model->created_at;
        $entity->updatedAt = $model->updated_at;

        return $entity;
    }

    public function isReadyToUse(): bool
    {
        return $this->status === GasCylinderStatus::FILLED;
    }

    public function isInUse(): bool
    {
        return $this->status === GasCylinderStatus::IN_USE;
    }

    public function isEmpty(): bool
    {
        return $this->status === GasCylinderStatus::EMPTY;
    }

    public function isInRefillProcess(): bool
    {
        return $this->status === GasCylinderStatus::REFILL_PROCESS;
    }

    public function isUnderMaintenance(): bool
    {
        return $this->status === GasCylinderStatus::MAINTENANCE;
    }

    public function isLost(): bool
    {
        return $this->status === GasCylinderStatus::LOST;
    }

    /**
     * Check if cylinder can transition to target status
     */
    public function canTransitionTo(GasCylinderStatus $targetStatus): bool
    {
        $allowedTransitions = [
            GasCylinderStatus::FILLED => [
                GasCylinderStatus::IN_USE,
                GasCylinderStatus::MAINTENANCE,
                GasCylinderStatus::EMPTY,
            ],
            GasCylinderStatus::IN_USE => [
                GasCylinderStatus::EMPTY,
                GasCylinderStatus::MAINTENANCE,
            ],
            GasCylinderStatus::EMPTY => [
                GasCylinderStatus::REFILL_PROCESS,
                GasCylinderStatus::LOST,
            ],
            GasCylinderStatus::REFILL_PROCESS => [
                GasCylinderStatus::FILLED,
                GasCylinderStatus::LOST,
            ],
            GasCylinderStatus::MAINTENANCE => [
                GasCylinderStatus::FILLED,
                GasCylinderStatus::EMPTY,
            ],
            GasCylinderStatus::LOST => [
                GasCylinderStatus::FILLED,
            ],
        ];

        $current = $this->status;
        return isset($allowedTransitions[$current])
            && in_array($targetStatus, $allowedTransitions[$current]);
    }

    public function incrementVersion(): void
    {
        $this->version++;
    }

    public function getIdentifier(): string
    {
        return "{$this->name} (SN: {$this->serialNumber})";
    }
}
