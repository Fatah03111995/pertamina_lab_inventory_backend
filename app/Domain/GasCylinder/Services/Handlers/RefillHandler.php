<?php

namespace App\Domain\GasCylinder\Services\Handlers;

use App\Enums\GasCylinderStatus;
use App\Enums\GasEventType;
use App\Models\GasCylinder as GasCylinderModel;
use App\Models\GasLocation as GasLocationModel;
use App\Models\User;
use App\Domain\GasCylinder\Entities\GasLocation;
use App\Domain\GasCylinder\Entities\GasCylinder;

// DB transactions are handled by caller; handlers must not start transactions here.

class RefillHandler extends BaseGasCylinderHandler
{
    public function takeForRefillMultiple(
        array $cylModels,
        GasLocationModel $vendorLocationModel,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId
    ): array {
        $events = [];
        foreach ($cylModels as $cylModel) {
            $cylinder = GasCylinder::fromModel($cylModel);
            $vendorLocation = GasLocation::fromModel($vendorLocationModel);

            $this->assertions->assertTakeForRefill($cylinder, $vendorLocation, $user, $metadata);

            $events[] = $this->performTransitionNoTransaction(
                $cylModel,
                $vendorLocationModel,
                GasCylinderStatus::REFILL_PROCESS,
                GasEventType::TAKE_FOR_REFILL,
                $user,
                $metadata,
                $notes,
                $transactionId
            );
        }

        return $events;
    }

    public function returnFromRefillMultiple(
        array $cylModels,
        GasLocationModel $storageLocationModel,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId
    ): array {
        $events = [];
        foreach ($cylModels as $cylModel) {
            $cylinder = GasCylinder::fromModel($cylModel);
            $storageLocation = GasLocation::fromModel($storageLocationModel);

            $this->assertions->assertReturnFromRefill($cylinder, $storageLocation, $user, $metadata);

            $events[] = $this->performTransitionNoTransaction(
                $cylModel,
                $storageLocationModel,
                GasCylinderStatus::FILLED,
                GasEventType::RETURN_FROM_REFILL,
                $user,
                $metadata,
                $notes,
                $transactionId
            );
        }

        return $events;
    }
}
