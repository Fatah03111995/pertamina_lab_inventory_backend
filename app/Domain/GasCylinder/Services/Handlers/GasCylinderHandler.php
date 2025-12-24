<?php

namespace App\Domain\GasCylinder\Services\Handlers;

use App\Enums\GasCylinderStatus;
use App\Exceptions\InvariantViolationException;
use App\Models\GasCylinder as GasCylinderModel;
use App\Models\GasLocation as GasLocationModel;
use App\Models\User;
use App\Domain\GasCylinder\Services\Validation\GasCylinderAssertions;
use App\Domain\GasCylinder\Services\Handlers\MovementHandler;
use App\Domain\GasCylinder\Services\Handlers\RefillHandler;
use App\Domain\GasCylinder\Services\Handlers\MaintenanceHandler;

/**
 * GasCylinderHandler - Facade Pattern
 *
 * Handler utama yang mendelegasikan operasi gas cylinder ke handler spesifik.
 * Berfungsi sebagai facade untuk memudahkan client dalam mengakses berbagai operasi.
 *
 * Struktur:
 * - MovementHandler: useCylinder, markEmpty, reportLost, resolveIssue
 * - RefillHandler: takeForRefill, returnFromRefill
 * - MaintenanceHandler: startMaintenance, endMaintenance
 */
class GasCylinderHandler
{
    protected GasCylinderAssertions $assertions;
    protected MovementHandler $movement;
    protected RefillHandler $refill;
    protected MaintenanceHandler $maintenance;

    public function __construct(?GasCylinderAssertions $assertions = null)
    {
        $this->assertions = $assertions ?? new GasCylinderAssertions();
        $this->movement = new MovementHandler($this->assertions); // dimasukkan untuk memastikan mendapatkan assertions yang sama
        $this->refill = new RefillHandler($this->assertions); // dimasukkan untuk memastikan mendapatkan assertions yang sama
        $this->maintenance = new MaintenanceHandler($this->assertions); // dimasukkan untuk memastikan mendapatkan assertions yang sama
    }

    public function useCylinders(
        array $cylModels,
        GasLocationModel $consumptionLocationModel,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId
    ) {
        return $this->movement->useCylinders($cylModels, $consumptionLocationModel, $user, $metadata, $notes, $transactionId);
    }

    public function externalMovementMultiple(
        array $cylModels,
        GasLocationModel $externalLocation,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId,
    ) {
        return $this->movement->movementExternalMultiple($cylModels, $externalLocation, $user, $metadata, $notes, $transactionId);
    }

    public function markEmptyMultiple(
        array $cylModels,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId
    ) {
        return $this->movement->markEmptyMultiple($cylModels, $user, $metadata, $notes, $transactionId);
    }

    public function takeForRefillMultiple(
        array $cylModels,
        GasLocationModel $vendorLocationModel,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId
    ) {
        return $this->refill->takeForRefillMultiple($cylModels, $vendorLocationModel, $user, $metadata, $notes, $transactionId);
    }

    public function returnFromRefillMultiple(
        array $cylModels,
        GasLocationModel $storageLocationModel,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId
    ) {
        return $this->refill->returnFromRefillMultiple($cylModels, $storageLocationModel, $user, $metadata, $notes, $transactionId);
    }

    public function startMaintenanceMultiple(
        array $cylModels,
        GasLocationModel $maintenanceLocationModel,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId = ''
    ) {
        return $this->maintenance->startMaintenanceMultiple($cylModels, $maintenanceLocationModel, $user, $metadata, $notes, $transactionId);
    }

    public function endMaintenanceMultiple(
        array $cylModels,
        GasLocationModel $storageLocationModel,
        User $user,
        array $metadata = [],
        string $notes = '',
        string $transactionId
    ) {
        return $this->maintenance->endMaintenanceMultiple($cylModels, $storageLocationModel, $user, $metadata, $notes, $transactionId);
    }

    public function reportLostMultiple(
        array $cylModels,
        User $user,
        string $notes,
        array $metadata = [],
        $transactionId,
    ) {
        return $this->movement->reportLostMultiple($cylModels, $user, $notes, $metadata, $transactionId);
    }

    public function resolveIssueMultiple(
        array $cylModels,
        GasLocationModel $toLocationModel,
        GasCylinderStatus $toStatus,
        User $user,
        string $notes,
        array $metadata = [],
        string $transactionId,
    ) {
        return $this->movement->resolveIssueMultiple($cylModels, $toLocationModel, $toStatus, $user, $notes, $metadata, $transactionId);
    }
}
