<?php

namespace App\Domain\GasCylinder\Entities;

use App\Enums\GasCompanyCategory;

/**
 * GasCompany Domain Entity
 * Represents a gas company (supplier/vendor/internal owner) in the domain layer
 */
class GasCompany
{
    public string $id;
    public string $name;
    public GasCompanyCategory $category;
    public string $address;
    public string $contact;
    public ?\DateTime $createdAt;
    public ?\DateTime $updatedAt;

    /**
     * Create a new GasCompany entity from a Model instance
     */
    public static function fromModel(\App\Models\GasCompany $model): self
    {
        $entity = new self();
        $entity->id = $model->id;
        $entity->name = $model->name;
        $entity->category = $model->category;
        $entity->address = $model->address;
        $entity->contact = $model->contact;
        $entity->createdAt = $model->created_at;
        $entity->updatedAt = $model->updated_at;

        return $entity;
    }

    /**
     * Check if company is external vendor/supplier
     */
    public function isExternal(): bool
    {
        return $this->category === GasCompanyCategory::EXTERNAL;
    }

    /**
     * Check if company is internal (owner)
     */
    public function isInternal(): bool
    {
        return $this->category === GasCompanyCategory::INTERNAL;
    }

    /**
     * Get identifier for logging/display
     */
    public function getIdentifier(): string
    {
        return "{$this->name} ({$this->category->value})";
    }
}
