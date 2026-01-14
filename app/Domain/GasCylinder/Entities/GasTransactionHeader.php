<?php

namespace App\Domain\GasCylinder\Entities;

use App\Enums\GasTransactionType;
use App\Domain\GasCylinder\Entities\GasTransaction;

/**
 * GasTransaction Domain Entity
 * Represents a transaction (batch of gas events) in the domain layer
 */
class GasTransactionHeader
{
    public string $id;
    public GasTransactionType $transactionType;
    public string $documentNumber;
    public string $evidenceDocument;
    public ?string $fromLocationId;
    public ?string $toLocationId;
    public string $notes;
    public string $createdBy;
    /** @var GasTransaction[] */
    public array $events;
    public ?\DateTime $createdAt;
    public ?\DateTime $updatedAt;

    /**
     * Create a new GasTransaction entity from a Model instance
     */
    public static function fromModel(\App\Models\GasTransactionHeader $model): self
    {
        $entity = new self();
        $entity->id = $model->id;
        $entity->transactionType = $model->transaction_type;
        $entity->evidenceDocument = $model->evidence_document;
        $entity->documentNumber = $model->document_number;
        $entity->fromLocationId = $model->from_location_id;
        $entity->toLocationId = $model->to_location_id;
        $entity->notes = $model->notes;
        $entity->createdBy = $model->created_by;
        $entity->createdAt = $model->created_at;
        $entity->updatedAt = $model->updated_at;

        // map events if available (each event is represented by GasTransaction model)
        $entity->events = [];
        if (isset($model->events) && is_iterable($model->events)) {
            foreach ($model->events as $ev) {
                $entity->events[] = GasTransaction::fromModel($ev);
            }
        }

        return $entity;
    }

    /**
     * Check if transaction is for refill operation
     */
    public function isRefillTransaction(): bool
    {
        return in_array($this->transactionType, [
            GasTransactionType::TAKE_FOR_REFILL,
            GasTransactionType::RETURN_FROM_REFILL,
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
        return "DOC: {$this->documentNumber} ({$this->transactionType->value})";
    }
}
