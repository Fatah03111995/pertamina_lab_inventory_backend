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

    /**
     * MOVEMENT TO CONSUMPTION LOCATION
     * Memindahkan silinder ke lokasi konsumsi dengan status IN_USE
     */
    public function useCylinder(
        GasCylinderModel $cylModel,
        GasLocationModel $consumptionLocationModel,
        User $user,
        array $metadata = []
    ) {
        return $this->movement->useCylinder($cylModel, $consumptionLocationModel, $user, $metadata);
    }

    /**
     * Batch: use multiple cylinders
     */
    public function useCylinders(array $cylModels, GasLocationModel $consumptionLocationModel, User $user, array $metadata = [], string $notes = '', string $transactionId = '')
    {
        return $this->movement->useCylinders($cylModels, $consumptionLocationModel, $user, $metadata, $notes, $transactionId);
    }

    /**
     * SETELAH PENGGUNAAN, TABUNG KOSONG
     * Menandai silinder sebagai kosong setelah digunakan
     */
    public function markEmpty(
        GasCylinderModel $cylModel,
        GasLocationModel $currentLocationModel,
        User $user,
        array $metadata = []
    ) {
        return $this->movement->markEmpty($cylModel, $currentLocationModel, $user, $metadata);
    }

    /**
     * Batch: mark multiple cylinders empty
     */
    public function markEmptyMultiple(array $cylModels, GasLocationModel $currentLocationModel, User $user, array $metadata = [], string $notes = '', string $transactionId = '')
    {
        return $this->movement->markEmptyMultiple($cylModels, $currentLocationModel, $user, $metadata, $notes, $transactionId);
    }

    /**
     * MOVEMENT TO REFILL VENDOR
     * Memindahkan silinder kosong ke vendor untuk pengisian ulang
     */
    public function takeForRefill(
        GasCylinderModel $cylModel,
        GasLocationModel $currentLocationModel,
        GasLocationModel $vendorLocationModel,
        User $user,
        array $metadata = [],
    ) {
        return $this->refill->takeForRefill($cylModel, $currentLocationModel, $vendorLocationModel, $user, $metadata);
    }

    /**
     * Batch: take multiple cylinders for refill
     */
    public function takeForRefillMultiple(array $cylModels, GasLocationModel $currentLocationModel, GasLocationModel $vendorLocationModel, User $user, array $metadata = [], string $notes = '', string $transactionId = '')
    {
        return $this->refill->takeForRefillMultiple($cylModels, $currentLocationModel, $vendorLocationModel, $user, $metadata, $notes, $transactionId);
    }

    /**
     * MOVEMENT FROM VENDOR AFTER REFILL
     * Mengembalikan silinder yang sudah diisi ulang dari vendor
     */
    public function returnFromRefill(
        GasCylinderModel $cylModel,
        GasLocationModel $storageLocationModel,
        User $user,
        array $metadata = [],
    ) {
        return $this->refill->returnFromRefill($cylModel, $storageLocationModel, $user, $metadata);
    }

    /**
     * Batch: return multiple cylinders from refill
     */
    public function returnFromRefillMultiple(array $cylModels, GasLocationModel $storageLocationModel, User $user, array $metadata = [], string $notes = '', string $transactionId = '')
    {
        return $this->refill->returnFromRefillMultiple($cylModels, $storageLocationModel, $user, $metadata, $notes, $transactionId);
    }

    /**
     * START MAINTENANCE
     * Memulai proses maintenance pada silinder
     */
    public function startMaintenance(
        GasCylinderModel $cylModel,
        GasLocationModel $maintenanceLocationModel,
        User $user,
        array $metadata = [],
    ) {
        return $this->maintenance->startMaintenance($cylModel, $maintenanceLocationModel, $user, $metadata);
    }

    /**
     * Batch: start maintenance for multiple cylinders
     */
    public function startMaintenanceMultiple(array $cylModels, GasLocationModel $maintenanceLocationModel, User $user, array $metadata = [], string $notes = '', string $transactionId = '')
    {
        return $this->maintenance->startMaintenanceMultiple($cylModels, $maintenanceLocationModel, $user, $metadata, $notes, $transactionId);
    }

    /**
     * END MAINTENANCE
     * Menyelesaikan maintenance dan mengembalikan silinder ke status tertentu
     */
    public function endMaintenance(
        GasCylinderModel $cylModel,
        GasLocationModel $toLocationModel,
        GasCylinderStatus $toStatus,
        User $user,
        array $metadata = [],
    ) {
        return $this->maintenance->endMaintenance($cylModel, $toLocationModel, $toStatus, $user, $metadata);
    }

    /**
     * Batch: end maintenance for multiple cylinders
     */
    public function endMaintenanceMultiple(array $cylModels, GasLocationModel $storageLocationModel, User $user, array $metadata = [], string $notes = '', string $transactionId = '')
    {
        return $this->maintenance->endMaintenanceMultiple($cylModels, $storageLocationModel, $user, $metadata, $notes, $transactionId);
    }

    /**
     * REPORT LOST
     * Melaporkan silinder sebagai hilang dengan catatan alasan
     */
    public function reportLost(
        GasCylinderModel $cylModel,
        User $user,
        string $notes,
        array $metadata = []
    ) {
        return $this->movement->reportLost($cylModel, $user, $notes, $metadata);
    }

    /**
     * Batch: report multiple cylinders lost
     */
    public function reportLostMultiple(array $cylModels, User $user, string $notes, array $metadata = [])
    {
        return $this->movement->reportLostMultiple($cylModels, $user, $notes, $metadata);
    }

    /**
     * RESOLVE ISSUE
     * Menyelesaikan issue pada silinder dengan catatan penjelasan
     */
    public function resolveIssue(
        GasCylinderModel $cylModel,
        GasLocationModel $toLocationModel,
        GasCylinderStatus $toStatus,
        User $user,
        string $notes,
        array $metadata = []
    ) {
        return $this->movement->resolveIssue($cylModel, $toLocationModel, $toStatus, $user, $notes, $metadata);
    }

    /**
     * Batch: resolve issue for multiple cylinders
     */
    public function resolveIssueMultiple(array $cylModels, GasLocationModel $toLocationModel, GasCylinderStatus $toStatus, User $user, string $notes, array $metadata = [])
    {
        return $this->movement->resolveIssueMultiple($cylModels, $toLocationModel, $toStatus, $user, $notes, $metadata);
    }
}
