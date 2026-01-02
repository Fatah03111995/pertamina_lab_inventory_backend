<?php

namespace App\Domain\GasCylinder\Services\Handlers;

use App\Enums\GasCylinderStatus;
use App\Enums\GasEventType;
use App\Exceptions\InvariantViolationException;
use App\Domain\GasCylinder\Entities\GasCylinder;
use App\Domain\GasCylinder\Entities\GasLocation;
use App\Models\GasCylinder as GasCylinderModel;
use App\Models\GasEvent;
use App\Models\GasLocation as GasLocationModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Domain\GasCylinder\Services\Validation\GasCylinderAssertions;

abstract class BaseGasCylinderHandler
{
    protected GasCylinderAssertions $assertions;

    public function __construct(?GasCylinderAssertions $assertions = null)
    {
        $this->assertions = $assertions ?? new GasCylinderAssertions();
    }

    /**
     * Core transactional operation to create event and update cylinder model
     */
    protected function moveAndTransition(
        GasCylinderModel $cylModel,
        ?GasLocationModel $toLocationModel,
        ?GasCylinderStatus $toStatus,
        GasEventType $eventType,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId = '',
    ): GasEvent {
        // Do not start a DB transaction here; caller should manage transactions.
        return $this->performTransitionNoTransaction(
            $cylModel,
            $toLocationModel,
            $toStatus,
            $eventType,
            $user,
            $metadata,
            $notes,
            $transactionId
        );
    }

    /**
     * Inner transition helper that does NOT start a DB transaction.
     * This can be called in a loop inside a single transaction for batch ops.
     */
    protected function performTransitionNoTransaction(
        GasCylinderModel $cylModel,
        ?GasLocationModel $toLocationModel,
        ?GasCylinderStatus $toStatus,
        GasEventType $eventType,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId = '',
    ): GasEvent {
        $cylModel->refresh();

        $fromStatus = $cylModel->status;
        $fromLocationId = $cylModel->current_location_id;
        $toLocationId = $toLocationModel->id ?? $fromLocationId;
        $toStatus = $toStatus ?? $fromStatus;
        $userId = $user->id;

        // invariant checks using domain entities
        $this->assertions->assertGeneral(
            GasCylinder::fromModel($cylModel),
            $toLocationModel ? GasLocation::fromModel($toLocationModel) : null,
            $toStatus,
            $eventType,
            $transactionId
        );

        //Create Event
        $event = GasEvent::create([
            'gas_cylinder_id' => $cylModel->id,
            'gas_transaction_id' => $transactionId ?: null,
            'event_type' => $eventType,
            'from_location_id' => $fromLocationId,
            'to_location_id' => $toLocationId,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'notes' => $notes,
            'created_by' => $userId,
            'metadata' => $metadata
        ]);

        //Update Gas Cylinder
        $cylModel->current_location_id = $toLocationId;
        $cylModel->status = $toStatus;
        $cylModel->version = $cylModel->version + 1;
        $cylModel->save();

        return $event;
    }
}
