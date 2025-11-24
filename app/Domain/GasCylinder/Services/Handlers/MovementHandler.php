<?php

namespace App\Domain\GasCylinder\Services\Handlers;

use App\Enums\GasCylinderStatus;
use App\Enums\GasEventType;
use App\Models\GasCylinder as GasCylinderModel;
use App\Models\GasLocation as GasLocationModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MovementHandler extends BaseGasCylinderHandler
{
    public function useCylinder(
        GasCylinderModel $cylModel,
        GasLocationModel $consumptionLocationModel,
        User $user,
        array $metadata = []
    ) {
        $cylinder = \App\Domain\GasCylinder\Entities\GasCylinder::fromModel($cylModel);
        $location = \App\Domain\GasCylinder\Entities\GasLocation::fromModel($consumptionLocationModel);

        $this->assertions->assertUseCylinder($cylinder, $location, $user, $metadata);

        return $this->moveAndTransition(
            $cylModel,
            $consumptionLocationModel,
            GasCylinderStatus::IN_USE,
            GasEventType::MOVEMENT_INTERNAL,
            $user,
            $metadata
        );
    }

    /**
     * Batch version: use multiple cylinders in a single DB transaction.
     * Accepts an array of GasCylinderModel objects.
     * Returns array of created GasEvent objects in same order.
     */
    public function useCylinders(array $cylModels, GasLocationModel $consumptionLocationModel, User $user, array $metadata = [], string $notes = '', string $transactionId = ''): array
    {
        return DB::transaction(function () use ($cylModels, $consumptionLocationModel, $user, $metadata, $notes, $transactionId) {
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
    public function markEmpty(
        GasCylinderModel $cylModel,
        GasLocationModel $currentLocationModel,
        User $user,
        array $metadata = []
    ) {
        $cylinder = \App\Domain\GasCylinder\Entities\GasCylinder::fromModel($cylModel);
        $location = \App\Domain\GasCylinder\Entities\GasLocation::fromModel($currentLocationModel);

        $this->assertions->assertMarkEmpty($cylinder, $location, $user, $metadata);

        return $this->moveAndTransition(
            $cylModel,
            null,
            GasCylinderStatus::EMPTY,
            GasEventType::USING,
            $user,
            $metadata
        );
    }

    /**
     * Batch version: mark multiple cylinders as empty in one transaction.
     */
    public function markEmptyMultiple(array $cylModels, GasLocationModel $currentLocationModel, User $user, array $metadata = [], string $notes = '', string $transactionId = ''): array
    {
        return DB::transaction(function () use ($cylModels, $currentLocationModel, $user, $metadata, $notes, $transactionId) {
            $events = [];
            foreach ($cylModels as $cylModel) {
                $cylinder = \App\Domain\GasCylinder\Entities\GasCylinder::fromModel($cylModel);
                $location = \App\Domain\GasCylinder\Entities\GasLocation::fromModel($currentLocationModel);

                $this->assertions->assertMarkEmpty($cylinder, $location, $user, $metadata);

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

    public function reportLost(
        GasCylinderModel $cylModel,
        User $user,
        string $notes,
        array $metadata = []
    ) {
        if (!$notes || empty($notes)) {
            throw new \App\Exceptions\InvariantViolationException('Harus Mencantumkan Alasan di Catatan');
        }

        return $this->moveAndTransition(
            $cylModel,
            null,
            GasCylinderStatus::LOST,
            GasEventType::REPORT_LOST,
            $user,
            $metadata,
            $notes
        );
    }

    /**
     * Batch version: report multiple cylinders lost in a single transaction.
     */
    public function reportLostMultiple(array $cylModels, User $user, string $notes, array $metadata = []): array
    {
        if (!$notes || empty($notes)) {
            throw new \App\Exceptions\InvariantViolationException('Harus Mencantumkan Alasan di Catatan');
        }

        return DB::transaction(function () use ($cylModels, $user, $notes, $metadata) {
            $events = [];
            foreach ($cylModels as $cylModel) {
                $events[] = $this->performTransitionNoTransaction(
                    $cylModel,
                    null,
                    GasCylinderStatus::LOST,
                    GasEventType::REPORT_LOST,
                    $user,
                    $metadata,
                    $notes
                );
            }

            return $events;
        });
    }

    public function resolveIssue(
        GasCylinderModel $cylModel,
        GasLocationModel $toLocationModel,
        GasCylinderStatus $toStatus,
        User $user,
        string $notes,
        array $metadata = []
    ) {
        if (!$notes || empty($notes)) {
            throw new \App\Exceptions\InvariantViolationException('Harus Mencantumkan Alasan di Catatan');
        }

        return $this->moveAndTransition(
            $cylModel,
            $toLocationModel,
            $toStatus,
            GasEventType::RESOLVE_ISSUE,
            $user,
            $metadata,
            $notes
        );
    }

    /**
     * Batch version: resolve issue for multiple cylinders in a single DB transaction.
     */
    public function resolveIssueMultiple(array $cylModels, GasLocationModel $toLocationModel, GasCylinderStatus $toStatus, User $user, string $notes, array $metadata = []): array
    {
        if (!$notes || empty($notes)) {
            throw new \App\Exceptions\InvariantViolationException('Harus Mencantumkan Alasan di Catatan');
        }

        return DB::transaction(function () use ($cylModels, $toLocationModel, $toStatus, $user, $notes, $metadata) {
            $events = [];
            foreach ($cylModels as $cylModel) {
                $events[] = $this->performTransitionNoTransaction(
                    $cylModel,
                    $toLocationModel,
                    $toStatus,
                    GasEventType::RESOLVE_ISSUE,
                    $user,
                    $metadata,
                    $notes
                );
            }

            return $events;
        });
    }
}
