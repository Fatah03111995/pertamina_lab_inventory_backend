<?php

namespace App\Domain\GasCylinder\Entities;

use App\Enums\GasLocationCategory;
use App\Models\GasLocation as GasLocationModel;

/**
 * GasLocation Domain Entity
 * Represents a location/place in the domain layer
 */
class GasLocation
{
    public string $id;
    public string $name;
    public string $code;
    public GasLocationCategory $category;
    public string $address;
    public ?\DateTime $createdAt;
    public ?\DateTime $updatedAt;

    /**
     * Create a new GasLocation entity from a Model instance
     */
    public static function fromModel(GasLocationModel $model): self
    {
        $entity = new self();
        $entity->id = $model->id;
        $entity->name = $model->name;
        $entity->code = $model->code;
        $entity->category = $model->category;
        $entity->address = $model->address;
        $entity->createdAt = $model->created_at;
        $entity->updatedAt = $model->updated_at;

        return $entity;
    }

    /**
     * Check if location is storage category
     */
    public function isStorage(): bool
    {
        return $this->category->isStorage();
    }

    /**
     * Check if location is maintenance category
     */
    public function isMaintenance(): bool
    {
        return $this->category->isMaintenance();
    }

    /**
     * Check if location is vendor category
     */
    public function isRefilling(): bool
    {
        return $this->category->isRefilling();
    }

    /**
     * Check if location is consumption category
     */
    public function isConsumption(): bool
    {
        return $this->category->isConsumption();
    }

    /**
     * Get location identifier for logging/display
     */
    public function getIdentifier(): string
    {
        return "{$this->name} ({$this->code}) - {$this->category->value}";
    }
}
