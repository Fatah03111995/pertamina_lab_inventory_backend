<?php

namespace App\Domain\GasCylinder\Services\Validation;

use App\Enums\GasCylinderStatus;
use App\Enums\GasEventType;
use App\Enums\GasLocationCategory;
use App\Exceptions\InvariantViolationException;
use App\Domain\GasCylinder\Entities\GasCylinder;
use App\Domain\GasCylinder\Entities\GasLocation;
use App\Models\GasCylinder as ModelsGasCylinder;
use App\Models\User;

class GasCylinderAssertions
{
    public function assertGeneral(
        GasCylinder $cyl,
        ?GasLocation $toLocation,
        ?GasCylinderStatus $toStatus,
        GasEventType $eventType,
        ?string $transactionId,
    ) {
        if ($eventType->requireEvidenceDocument() && !$transactionId) {
            throw new InvariantViolationException(
                "Event {$eventType->value} harus mencantumkan nomor transaksi."
            );
        }
    }

    public function assertUseCylinder(
        GasCylinder $cyl,
        GasLocation $consumptionLocation,
        User $user,
        array $metadata = []
    ) {
        if (!$cyl->isReadyToUse()) {
            throw new InvariantViolationException("{$cyl->name} status : {$cyl->status->value}, status gas should be " . GasCylinderStatus::FILLED->value);
        }
        if (!$consumptionLocation->isConsumption()) {
            throw new InvariantViolationException("{$cyl->name} to location category : {$consumptionLocation->category->value}, It must " . GasLocationCategory::CONSUMPTION->value);
        }
    }

    public function assertMovementExternal(
        GasCylinder $cyl,
        GasLocation $destinationLocation,
        User $user,
        array $metadata = []
    ) {
    }

    public function assertMarkEmpty(
        GasCylinder $cyl,
        User $user,
        array $metadata = []
    ) {
        if (!in_array($cyl->status, [
            GasCylinderStatus::FILLED,
            GasCylinderStatus::IN_USE
        ])) {
            throw new InvariantViolationException("{$cyl->name} status : {$cyl->status->value}, status gas should be "
            .GasCylinderStatus::FILLED->value
            ." or "
            .GasCylinderStatus::IN_USE->value);
        }
        $currentLocation = ModelsGasCylinder::find($cyl->id);
        if (!$currentLocation->isConsumption()) {
            throw new InvariantViolationException("{$cyl->name} location is not in CONSUMPTION category");
        }
    }

    public function assertTakeForRefill(
        GasCylinder $cyl,
        GasLocation $vendorLocation,
        User $user,
        array $metadata = [],
    ) {
        $currentLocation = ModelsGasCylinder::find($cyl->id);
        if ($cyl->status !== GasCylinderStatus::EMPTY) {
            throw new InvariantViolationException("{$cyl->name} status : {$cyl->status->value}, status gas should be "
            .GasCylinderStatus::EMPTY->value);
        }
        if (!$currentLocation->isStorage()) {
            throw new InvariantViolationException("{$cyl->name} current location category must be " . GasLocationCategory::STORAGE->value);
        }
        if (!$vendorLocation->isVendor()) {
            throw new InvariantViolationException(
                "Gas destination category : {$vendorLocation->category->value}, it should be "
            .GasLocationCategory::VENDOR->value
            );
        }
    }

    public function assertReturnFromRefill(
        GasCylinder $cyl,
        GasLocation $storageLocation,
        User $user,
        array $metadata = [],
    ) {
        if ($cyl->status !== GasCylinderStatus::REFILL_PROCESS) {
            throw new InvariantViolationException(
                "{$cyl->name} status: {$cyl->status->value}, it should be "
            .GasCylinderStatus::REFILL_PROCESS->value
            );
        }
        if (!$storageLocation->isStorage()) {
            throw new InvariantViolationException(
                "Gas destination category : {$storageLocation->category->value}, it should be "
            .GasLocationCategory::STORAGE->value
            );
        }
    }

    public function assertStartMaintenance(
        GasCylinder $cyl,
        GasLocation $maintenanceLocation,
        User $user,
        array $metadata = [],
    ) {
        if (!$maintenanceLocation->isMaintenance()) {
            throw new InvariantViolationException(
                "Gas destination category : {$maintenanceLocation->category->value}, it should be "
                . GasLocationCategory::MAINTENANCE->value
            );
        }
    }

    public function assertEndMaintenance(
        GasCylinder $cyl,
        GasLocation $toLocation,
        GasCylinderStatus $toStatus,
        User $user,
        array $metadata = [],
    ) {
        if ($toLocation->isMaintenance()) {
            throw new InvariantViolationException(
                "Gas destination after maintenance should not be in category "
            .GasLocationCategory::MAINTENANCE->value
            );
        }
        if ($cyl->status !== GasCylinderStatus::MAINTENANCE) {
            throw new InvariantViolationException(
                "{$cyl->name} status: {$cyl->status->value}, can't use "
                . GasEventType::MAINTENANCE_END->value
            );
        }
    }
}
