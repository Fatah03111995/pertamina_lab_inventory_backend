<?php

namespace App\Domain\GasCylinder\Services\Handlers;

use App\Enums\GasCylinderStatus;
use App\Enums\GasEventType;
use App\Models\GasCylinder as GasCylinderModel;
use Illuminate\Support\Facades\DB;
use App\Models\GasLocation as GasLocationModel;
use App\Domain\GasCylinder\Entities\GasCylinder;
use App\Domain\GasCylinder\Entities\GasLocation;
use App\Models\User;

class MaintenanceHandler extends BaseGasCylinderHandler
{
    public function startMaintenance(
        GasCylinderModel $cylModel,
        GasLocationModel $maintenanceLocationModel,
        User $user,
        array $metadata = [],
    ) {
        $cylinder = GasCylinder::fromModel($cylModel);
        $maintenanceLocation = GasLocation::fromModel($maintenanceLocationModel);

        $this->assertions->assertStartMaintenance($cylinder, $maintenanceLocation, $user, $metadata);

        return $this->moveAndTransition(
            $cylModel,
            $maintenanceLocationModel,
            GasCylinderStatus::MAINTENANCE,
            GasEventType::MAINTENANCE_START,
            $user,
            $metadata
        );
    }

    public function endMaintenance(
        GasCylinderModel $cylModel,
        GasLocationModel $toLocationModel,
        GasCylinderStatus $toStatus,
        User $user,
        array $metadata = [],
    ) {
        $cylinder = \App\Domain\GasCylinder\Entities\GasCylinder::fromModel($cylModel);
        $toLocation = \App\Domain\GasCylinder\Entities\GasLocation::fromModel($toLocationModel);

        $this->assertions->assertEndMaintenance($cylinder, $toLocation, $toStatus, $user, $metadata);

        return $this->moveAndTransition(
            $cylModel,
            $toLocationModel,
            $toStatus,
            GasEventType::MAINTENANCE_END,
            $user,
            $metadata
        );
    }

    /**
     * Batch version: start maintenance for multiple cylinders inside single transaction.
     */
    public function startMaintenanceMultiple(array $cylModels, GasLocationModel $maintenanceLocationModel, User $user, array $metadata = [], string $notes = '', string $transactionId = ''): array
    {
        return DB::transaction(function () use ($cylModels, $maintenanceLocationModel, $user, $metadata, $notes, $transactionId) {
            $events = [];
            foreach ($cylModels as $cylModel) {
                $cylinder = \App\Domain\GasCylinder\Entities\GasCylinder::fromModel($cylModel);
                $maintenanceLocation = \App\Domain\GasCylinder\Entities\GasLocation::fromModel($maintenanceLocationModel);

                $this->assertions->assertStartMaintenance($cylinder, $maintenanceLocation, $user, $metadata);

                $events[] = $this->performTransitionNoTransaction(
                    $cylModel,
                    $maintenanceLocationModel,
                    GasCylinderStatus::MAINTENANCE,
                    GasEventType::MAINTENANCE_START,
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
     * Batch version: end maintenance for multiple cylinders inside single transaction.
     */
    public function endMaintenanceMultiple(array $cylModels, GasLocationModel $storageLocationModel, User $user, array $metadata = [], string $notes = '', string $transactionId = ''): array
    {
        return DB::transaction(function () use ($cylModels, $storageLocationModel, $user, $metadata, $notes, $transactionId) {
            $events = [];
            foreach ($cylModels as $cylModel) {
                $cylinder = \App\Domain\GasCylinder\Entities\GasCylinder::fromModel($cylModel);
                $storageLocation = \App\Domain\GasCylinder\Entities\GasLocation::fromModel($storageLocationModel);

                $this->assertions->assertEndMaintenance($cylinder, $storageLocation, GasCylinderStatus::FILLED, $user, $metadata);

                $events[] = $this->performTransitionNoTransaction(
                    $cylModel,
                    $storageLocationModel,
                    GasCylinderStatus::FILLED,
                    GasEventType::MAINTENANCE_END,
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
