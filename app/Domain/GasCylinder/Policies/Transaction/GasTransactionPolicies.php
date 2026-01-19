<?php

namespace App\Domain\GasCylinder\Policies\Transaction;

use App\Domain\GasCylinder\Entities\GasCylinder;
use App\Domain\GasCylinder\Entities\GasLocation;
use App\Domain\GasCylinder\Policies\Transaction\GasMovementAllowed;
use App\Enums\GasTransactionType;
use App\Exceptions\InvariantViolationException;
use App\Enums\GasCylinderStatus;
use App\Models\GasCylinder as GasCylinderModel;
use App\Models\GasLocation as GasLocationModel;
use App\Models\User as UserModel;
use App\Models\GasTransaction as GasTransactionModel;
use App\Models\GasTransactionHeader;

class GasTransactionPolicies
{
    private readonly GasMovementAllowed $gasMovementAllowed;
    public function __construct(
        ?GasMovementAllowed $gasMovementAllowed = null
    ) {
        $this->gasMovementAllowed = $gasMovementAllowed ?? new GasMovementAllowed();
    }
    public function assert(
        GasCylinderModel $cylModel,
        ?string $documentNumber,
        ?string $evidenceDocument,
        GasTransactionType $transactionType,
        GasLocationModel $fromLocation,
        GasLocationModel $toLocation,
        GasCylinderStatus $fromStatus,
        GasCylinderStatus $toStatus,
        string $notes,
        UserModel $user,
        array $metadata,
    ): void {
        $cylEntity = GasCylinder::fromModel($cylModel);
        $fromLocEntity = GasLocation::fromModel($fromLocation);
        $toLocEntity = GasLocation::fromModel($toLocation);

        if ($fromStatus->isLost() && $transactionType !== GasTransactionType::RESOLVE_ISSUE) {
            throw new InvariantViolationException(
                'Gas cylinder '
                . $cylEntity->getIdentifier()
                . ' is LOST based on Data, please resolve this issue first'
            );
        }

        if ($cylEntity->currentLocationId !== $fromLocation->id) {
            throw new InvariantViolationException(
                'Gas cylinder '
                . $cylEntity->getIdentifier()
                . ' is not current located at '
                . $fromLocEntity->getIdentifier()
            );
        }

        if ($transactionType->requireEvidenceDocument()) {
            if (empty($documentNumber)) {
                throw new InvariantViolationException('Transaction type : '.$transactionType->value . ', need document number');
            }
            if (empty($evidenceDocument)) {
                throw new InvariantViolationException('Transaction type' . $transactionType->value . ', need document evidence');
            }
        }

        if ($transactionType->requireNotes()) {
            if (empty($notes)) {
                throw new InvariantViolationException('This transaction requires notes to be provided');
            }
        }

        if (!$this->gasMovementAllowed->isMoveChangeAllowed(
            $transactionType,
            $fromLocEntity->category,
            $toLocEntity->category
        )) {
            throw new InvariantViolationException('Gas movement is not allowed for this transaction type');
        }

        if (!$this->gasMovementAllowed->isStatusChangeAllowed(
            $transactionType,
            $fromStatus,
            $toStatus
        )) {
            throw new InvariantViolationException(
                'Gas Cylinder ' . $cylEntity->getIdentifier()
                . ' status can not be changed from '
                . $fromStatus->value
                . ' to '
                . $toStatus->value
                . ' in this transaction type'
            );
        }
    }
}
