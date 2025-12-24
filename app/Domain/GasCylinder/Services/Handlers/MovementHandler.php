<?php

namespace App\Domain\GasCylinder\Services\Handlers;

use App\Enums\GasCylinderStatus;
use App\Enums\GasEventType;
use App\Models\GasCylinder as GasCylinderModel;
use App\Models\GasLocation as GasLocationModel;
use App\Models\User;
use App\Domain\GasCylinder\Entities\GasCylinder;
use App\Domain\GasCylinder\Entities\GasLocation;
use Illuminate\Support\Facades\DB;

class MovementHandler extends BaseGasCylinderHandler
{
    public function useCylinders(
        array $cylModels,
        GasLocationModel $consumptionLocationModel,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId,
    ): array {
        return DB::transaction(function () use (
            $cylModels,
            $consumptionLocationModel,
            $user,
            $metadata,
            $notes,
            $transactionId,
        ) {
            $events = [];
            foreach ($cylModels as $cylModel) {
                $events[] = $this->performTransitionNoTransaction(
                    $cylModel,
                    $consumptionLocationModel,
                    GasCylinderStatus::IN_USE,
                    GasEventType::MOVEMENT_INTERNAL,
                    $user,
                    $metadata,
                    $notes,
                    $transactionId
                );
            }

            return $events;
        });
    }

    public function movementExternalMultiple(
        array $cylModels,
        GasLocationModel $externalLocation,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId,
    ): array {
        return DB::transaction(function () use (
            $cylModels,
            $externalLocation,
            $user,
            $metadata,
            $notes,
            $transactionId,
        ) {
            $events = [];

            foreach ($cylModels as $cylModel) {
                $cylinder = GasCylinder::fromModel($cylModel);
                $destinationLocation = GasLocation::fromModel($externalLocation);
                $this->assertions->assertMovementExternal($cylinder, $destinationLocation, $user, $metadata);
            }
            return $events;
        });
    }

    public function markEmptyMultiple(
        array $cylModels,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId
    ): array {
        return DB::transaction(function () use (
            $cylModels,
            $user,
            $metadata,
            $notes,
            $transactionId,
        ) {
            $events = [];
            foreach ($cylModels as $cylModel) {
                $cylinder = GasCylinder::fromModel($cylModel);

                $this->assertions->assertMarkEmpty($cylinder, $user, $metadata);

                $events[] = $this->performTransitionNoTransaction(
                    $cylModel,
                    null,
                    GasCylinderStatus::EMPTY,
                    GasEventType::USING,
                    $user,
                    $metadata,
                    $notes,
                    $transactionId
                );
            }

            return $events;
        });
    }

    public function reportLostMultiple(
        array $cylModels,
        User $user,
        string $notes,
        array $metadata = [],
        string $transactionId
    ): array {
        if (!$notes || empty($notes)) {
            throw new \App\Exceptions\InvariantViolationException('Harus Mencantumkan Alasan di Catatan');
        }

        return DB::transaction(function () use ($cylModels, $user, $notes, $metadata, $transactionId) {
            $events = [];
            foreach ($cylModels as $cylModel) {
                $events[] = $this->performTransitionNoTransaction(
                    $cylModel,
                    null,
                    GasCylinderStatus::LOST,
                    GasEventType::REPORT_LOST,
                    $user,
                    $metadata,
                    $notes,
                    $transactionId
                );
            }

            return $events;
        });
    }

    public function resolveIssueMultiple(
        array $cylModels,
        GasLocationModel $toLocationModel,
        GasCylinderStatus $toStatus,
        User $user,
        string $notes,
        array $metadata = [],
        string $transactionId
    ): array {
        if (!$notes || empty($notes)) {
            throw new \App\Exceptions\InvariantViolationException('Harus Mencantumkan Alasan di Catatan');
        }

        return DB::transaction(function () use (
            $cylModels,
            $toLocationModel,
            $toStatus,
            $user,
            $notes,
            $metadata,
            $transactionId,
        ) {
            $events = [];
            foreach ($cylModels as $cylModel) {
                $events[] = $this->performTransitionNoTransaction(
                    $cylModel,
                    $toLocationModel,
                    $toStatus,
                    GasEventType::RESOLVE_ISSUE,
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
