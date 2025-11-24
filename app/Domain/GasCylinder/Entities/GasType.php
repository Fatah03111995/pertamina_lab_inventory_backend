<?php

namespace App\Domain\GasCylinder\Entities;

/**
 * GasType Domain Entity
 * Represents a type of gas in the domain layer
 */
class GasType
{
    public int $id;
    public string $name;
    public int $minStock;
    public string $safetyInfo;
    public string $description;
    public ?\DateTime $createdAt;
    public ?\DateTime $updatedAt;

    /**
     * Create a new GasType entity from a Model instance
     */
    public static function fromModel(\App\Models\GasType $model): self
    {
        $entity = new self();
        $entity->id = $model->id;
        $entity->name = $model->name;
        $entity->minStock = $model->min_stock ?? 0;
        $entity->safetyInfo = $model->safety_info ?? '';
        $entity->description = $model->description ?? '';
        $entity->createdAt = $model->created_at;
        $entity->updatedAt = $model->updated_at;

        return $entity;
    }

    /**
     * Check if current stock is below minimum
     */
    public function isBelowMinimumStock(int $currentStock): bool
    {
        return $currentStock < $this->minStock;
    }

    /**
     * Get identifier for logging/display
     */
    public function getIdentifier(): string
    {
        return "{$this->name} (Min Stock: {$this->minStock})";
    }
}
