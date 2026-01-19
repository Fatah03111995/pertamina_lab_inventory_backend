<?php

namespace App\Domain\GasCylinder\Entities;

use App\Enums\GasCylinderStatus;
use App\Models\GasCylinder as ModelGasCylinder;

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
    public static function fromModel(ModelGasCylinder $model): self
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
        return $this->status->isFilled();
    }

    public function isInUse(): bool
    {
        return $this->status->isInUse();
    }

    public function isEmpty(): bool
    {
        return $this->status->isEmpty();
    }

    public function isInRefillProcess(): bool
    {
        return $this->status->isRefillProcess();
    }

    public function isUnderMaintenance(): bool
    {
        return $this->status->isMaintenance();
    }

    public function isLost(): bool
    {
        return $this->status->isLost();
    }


    public function getIdentifier(): string
    {
        return "{$this->name} (SN: {$this->serialNumber})";
    }
}
