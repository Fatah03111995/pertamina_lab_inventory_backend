<?php

namespace App\Domain\GasCylinder\Entities;

use App\Enums\GasEventType;

/**
 * GasTransaction Domain Entity
 * Represents a transaction (batch of gas events) in the domain layer
 */
class GasTransaction
{
    public string $id;
    public string $documentNumber;
    public GasEventType $eventType;
    public string $companyId;
    public string $notes;
    public string $createdBy;
    public ?\DateTime $createdAt;
    public ?\DateTime $updatedAt;

    /**
     * Create a new GasTransaction entity from a Model instance
     */
    public static function fromModel(\App\Models\GasTransaction $model): self
    {
        $entity = new self();
        $entity->id = $model->id;
        $entity->documentNumber = $model->document_number;
        $entity->eventType = $model->event_type;
        $entity->companyId = $model->company_id;
        $entity->notes = $model->notes;
        $entity->createdBy = $model->created_by;
        $entity->createdAt = $model->created_at;
        $entity->updatedAt = $model->updated_at;

        return $entity;
    }

    /**
     * Check if transaction is for refill operation
     */
    public function isRefillTransaction(): bool
    {
        return in_array($this->eventType, [
            GasEventType::TAKE_FOR_REFILL,
            GasEventType::RETURN_FROM_REFILL,
        ]);
    }

    /**
     * Check if transaction requires document validation
     */
    public function requiresDocumentValidation(): bool
    {
        return !empty($this->documentNumber);
    }

    /**
     * Get identifier for logging/display
     */
    public function getIdentifier(): string
    {
        return "DOC: {$this->documentNumber} ({$this->eventType->value})";
    }
}
