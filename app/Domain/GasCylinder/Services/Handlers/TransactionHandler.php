<?php

namespace App\Domain\GasCylinder\Services\Handlers;

use App\Domain\GasCylinder\Services\Transaction\GasTransactionService;
use App\Models\GasLocation as GasLocationModel;
use App\Models\User as UserModel;
use App\Enums\GasCylinderStatus;
use App\Enums\GasTransactionType;
use App\Models\GasTransactionHeader;

class TransactionHandler
{
    public static function create(
        GasTransactionType $transactionType,
        array $cylModels,
        ?string $documentNumber,
        ?string $evidenceDocument,
        ?GasLocationModel $fromLocation,
        ?GasLocationModel $toLocation,
        ?GasCylinderStatus $toStatus,
        string $notes,
        UserModel $user,
        array $metadata,
    ): GasTransactionHeader {
        $service = new GasTransactionService();
        return match($transactionType) {
            GasTransactionType::MOVEMENT
            => $service->movementMultiple($cylModels, $documentNumber, $evidenceDocument, $fromLocation, $toLocation, $toStatus, $notes, $user, $metadata),
            GasTransactionType::MARK_EMPTY
            => $service->markEmpty($cylModels, $documentNumber, $evidenceDocument, $fromLocation, $toLocation, $toStatus, $notes, $user, $metadata),
            GasTransactionType::TAKE_FOR_REFILL
            => $service->takeForRefill($cylModels, $documentNumber, $evidenceDocument, $fromLocation, $toLocation, $notes, $user, $metadata),
            GasTransactionType::RETURN_FROM_REFILL
            => $service->returnFromRefill($cylModels, $documentNumber, $evidenceDocument, $fromLocation, $toLocation, $notes, $user, $metadata),
            GasTransactionType::TAKE_FOR_MAINTENANCE
            => $service->takeForMaintenance($cylModels, $documentNumber, $evidenceDocument, $fromLocation, $toLocation, $notes, $user, $metadata),
            GasTransactionType::RETURN_FROM_MAINTENANCE
            => $service->returnFromMaintenance($cylModels, $documentNumber, $evidenceDocument, $fromLocation, $toLocation, $toStatus, $notes, $user, $metadata),
            GasTransactionType::REPORT_LOST
            => $service->reportLost($cylModels, $documentNumber, $evidenceDocument, $notes, $user, $metadata),
            GasTransactionType::RESOLVE_ISSUE
            => $service->resolveIssue($cylModels, $documentNumber, $evidenceDocument, $fromLocation, $toLocation, $toStatus, $notes, $user, $metadata),
        };
    }
    public static function update()
    {
    }
    public static function delete()
    {
    }
}
