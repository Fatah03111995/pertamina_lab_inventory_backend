<?php

use App\Enums\GasEventType;
use App\Exceptions\InvariantViolationException;
use App\Models\User;

class GasTransactionAssertions
{
    public function assertGeneral(
        GasEventType $eventType,
        string $documentNumber,
        string $evidenceDocument,
        string $notes = '',
        User $user,
    ) {
        if ($eventType->requireEvidenceDocument()) {
            if (empty($documentNumber)) {
                throw new InvariantViolationException('this event type : '.$eventType->value . ', need document number');
            }
            if (empty($evidenceDocument)) {
                throw new InvariantViolationException('this event type' . $eventType->value . ', need document evidence');
            }
        }
    }
}
