<?php

namespace App\Domain\GasCylinder\Services\Handlers;

use App\Enums\GasCylinderStatus;
use App\Enums\GasEventType;
use App\Models\GasCylinder as GasCylinderModel;
use App\Models\GasLocation as GasLocationModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RefillHandler extends BaseGasCylinderHandler
{
    public function takeForRefill(
        GasCylinderModel $cylModel,
        GasLocationModel $currentLocationModel,
        GasLocationModel $vendorLocationModel,
        User $user,
        array $metadata = [],
    ) {
        $cylinder = \App\Domain\GasCylinder\Entities\GasCylinder::fromModel($cylModel);
        $currentLocation = \App\Domain\GasCylinder\Entities\GasLocation::fromModel($currentLocationModel);
        $vendorLocation = \App\Domain\GasCylinder\Entities\GasLocation::fromModel($vendorLocationModel);

        $this->assertions->assertTakeForRefill($cylinder, $currentLocation, $vendorLocation, $user, $metadata);

        return $this->moveAndTransition(
            $cylModel,
            $vendorLocationModel,
            GasCylinderStatus::REFILL_PROCESS,
            GasEventType::TAKE_FOR_REFILL,
            $user,
            $metadata
        );
    }

    public function returnFromRefill(
        GasCylinderModel $cylModel,
        GasLocationModel $storageLocationModel,
        User $user,
        array $metadata = [],
    ) {
        $cylinder = \App\Domain\GasCylinder\Entities\GasCylinder::fromModel($cylModel);
        $storageLocation = \App\Domain\GasCylinder\Entities\GasLocation::fromModel($storageLocationModel);

        $this->assertions->assertReturnFromRefill($cylinder, $storageLocation, $user, $metadata);

        return $this->moveAndTransition(
            $cylModel,
            $storageLocationModel,
            GasCylinderStatus::FILLED,
            GasEventType::RETURN_FROM_REFILL,
            $user,
            $metadata,
        );
    }

    /**
     * Batch version: take multiple cylinders for refill in a single DB transaction.
     * Returns array of GasEvent created.
     */
    public function takeForRefillMultiple(array $cylModels, GasLocationModel $currentLocationModel, GasLocationModel $vendorLocationModel, User $user, array $metadata = [], string $notes = '', string $transactionId = ''): array
    {
        return DB::transaction(function () use ($cylModels, $currentLocationModel, $vendorLocationModel, $user, $metadata, $notes, $transactionId) {
            $events = [];
            foreach ($cylModels as $cylModel) {
                $cylinder = \App\Domain\GasCylinder\Entities\GasCylinder::fromModel($cylModel);
                $currentLocation = \App\Domain\GasCylinder\Entities\GasLocation::fromModel($currentLocationModel);
                $vendorLocation = \App\Domain\GasCylinder\Entities\GasLocation::fromModel($vendorLocationModel);

                $this->assertions->assertTakeForRefill($cylinder, $currentLocation, $vendorLocation, $user, $metadata);

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
        });
    }

    /**
     * Batch version: return multiple cylinders from refill in a single DB transaction.
     */
    public function returnFromRefillMultiple(array $cylModels, GasLocationModel $storageLocationModel, User $user, array $metadata = [], string $notes = '', string $transactionId = ''): array
    {
        return DB::transaction(function () use ($cylModels, $storageLocationModel, $user, $metadata, $notes, $transactionId) {
            $events = [];
            foreach ($cylModels as $cylModel) {
                $cylinder = \App\Domain\GasCylinder\Entities\GasCylinder::fromModel($cylModel);
                $storageLocation = \App\Domain\GasCylinder\Entities\GasLocation::fromModel($storageLocationModel);

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
        });
    }
}
