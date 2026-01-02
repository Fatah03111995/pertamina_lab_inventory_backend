<?php

namespace App\Domain\GasCylinder\Services\Handlers;

use App\Enums\GasCylinderStatus;
use App\Enums\GasEventType;
use App\Models\GasCylinder as GasCylinderModel;
use App\Models\GasLocation as GasLocationModel;
use App\Domain\GasCylinder\Entities\GasCylinder;
use App\Domain\GasCylinder\Entities\GasLocation;
use App\Models\User;

class MaintenanceHandler extends BaseGasCylinderHandler
{
    public function startMaintenanceMultiple(
        array $cylModels,
        GasLocationModel $maintenanceLocationModel,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId
    ): array {
        $events = [];
        foreach ($cylModels as $cylModel) {
            $cylinder = GasCylinder::fromModel($cylModel);
            $maintenanceLocation = GasLocation::fromModel($maintenanceLocationModel);

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
    }

    public function endMaintenanceMultiple(
        array $cylModels,
        GasLocationModel $storageLocationModel,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId = ''
    ): array {
        $events = [];
        foreach ($cylModels as $cylModel) {
            $cylinder = GasCylinder::fromModel($cylModel);
            $storageLocation = GasLocation::fromModel($storageLocationModel);

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
    }
}
