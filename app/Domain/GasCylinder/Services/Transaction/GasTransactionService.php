<?php

namespace App\Domain\GasCylinder\Services\Transaction;

use App\Domain\GasCylinder\Policies\Transaction\GasTransactionPolicies;
use App\Enums\GasCylinderStatus;
use App\Models\GasLocation as GasLocationModel;
use App\Models\GasTransactionHeader;
use App\Models\User as UserModel;

class GasTransactionService
{
    private GasTransactionPolicies $policies;
    protected MovementService $movementService;
    protected RefillService $refillService;
    protected MaintenanceService $maintenanceService;
    protected IssueService $issueService;

    public function __construct(
        ?GasTransactionPolicies $policies = null
    ) {
        $this->policies = $policies ?? new GasTransactionPolicies();
        $this->maintenanceService = new MaintenanceService($this->policies);
        $this->movementService = new MovementService($this->policies);
        $this->refillService = new RefillService($this->policies);
        $this->issueService = new IssueService($this->policies);
    }

    public function movementMultiple(
        /** @param App\Models\GasCylinder */
        array $cylModels,
        ?string $documentNumber,
        ?string $evidenceDocument,
        ?GasLocationModel $fromLocation,
        ?GasLocationModel $toLocation,
        ?GasCylinderStatus $toStatus,
        string $notes,
        UserModel $user,
        array $metadata,
    ): GasTransactionHeader {
        if (!$fromLocation) {
            throw new \Exception('Parameter from location requires to be provided');
        }
        if (!$toLocation) {
            throw new \Exception('Parameter to location requires to be provided');
        }
        return $this->movementService->movement(
            $cylModels,
            $documentNumber,
            $evidenceDocument,
            $fromLocation,
            $toLocation,
            $toStatus,
            $notes,
            $user,
            $metadata
        );
    }

    public function markEmpty(
        /** @param App\Models\GasCylinder */
        array $cylModels,
        ?string $documentNumber,
        ?string $evidenceDocument,
        ?GasLocationModel $fromLocation,
        ?GasLocationModel $toLocation,
        ?GasCylinderStatus $toStatus,
        string $notes,
        UserModel $user,
        array $metadata,
    ): GasTransactionHeader {
        if (!$fromLocation) {
            throw new \Exception('Parameter from location requires to be provided');
        }
        if (!$toLocation) {
            throw new \Exception('Parameter to location requires to be provided');
        }
        if (!$toStatus) {
            throw new \Exception('Parameter to Status requires to be provided');
        }
        return $this->markEmpty(
            $cylModels,
            $documentNumber,
            $evidenceDocument,
            $fromLocation,
            $toLocation,
            $toStatus,
            $notes,
            $user,
            $metadata
        );
    }

    public function takeForMaintenance(
        /** @param App\Models\GasCylinder */
        array $cylModels,
        ?string $documentNumber,
        ?string $evidenceDocument,
        ?GasLocationModel $fromLocation,
        ?GasLocationModel $toLocation,
        string $notes,
        UserModel $user,
        array $metadata,
    ): GasTransactionHeader {
        if (!$fromLocation) {
            throw new \Exception('Parameter from location requires to be provided');
        }
        if (!$toLocation) {
            throw new \Exception('Parameter to location requires to be provided');
        }

        return $this->maintenanceService->takeForMaintenance(
            $cylModels,
            $documentNumber,
            $evidenceDocument,
            $fromLocation,
            $toLocation,
            $notes,
            $user,
            $metadata
        );
    }

    public function returnFromMaintenance(
        /** @param App\Models\GasCylinder */
        array $cylModels,
        ?string $documentNumber,
        ?string $evidenceDocument,
        ?GasLocationModel $fromLocation,
        ?GasLocationModel $toLocation,
        ?GasCylinderStatus $toStatus,
        string $notes,
        UserModel $user,
        array $metadata,
    ): GasTransactionHeader {
        if (!$fromLocation) {
            throw new \Exception('Parameter from location requires to be provided');
        }
        if (!$toLocation) {
            throw new \Exception('Parameter to location requires to be provided');
        }
        if (!$toStatus) {
            throw new \Exception('Parameter to Status requires to be provided');
        }
        return $this->maintenanceService->returnFromMaintenance(
            $cylModels,
            $documentNumber,
            $evidenceDocument,
            $fromLocation,
            $toLocation,
            $toStatus,
            $notes,
            $user,
            $metadata
        );
    }

    public function reportLost(
        /** @param App\Models\GasCylinder */
        array $cylModels,
        ?string $documentNumber,
        ?string $evidenceDocument,
        string $notes,
        UserModel $user,
        array $metadata,
    ): GasTransactionHeader {

        return $this->issueService->reportLost(
            $cylModels,
            $documentNumber,
            $evidenceDocument,
            $notes,
            $user,
            $metadata
        );
    }

    public function resolveIssue(
        /** @param App\Models\GasCylinder */
        array $cylModels,
        ?string $documentNumber,
        ?string $evidenceDocument,
        ?GasLocationModel $fromLocation,
        ?GasLocationModel $toLocation,
        ?GasCylinderStatus $toStatus,
        string $notes,
        UserModel $user,
        array $metadata,
    ): GasTransactionHeader {
        if (!$fromLocation) {
            throw new \Exception('Parameter from location requires to be provided');
        }
        if (!$toLocation) {
            throw new \Exception('Parameter to location requires to be provided');
        }
        if (!$toStatus) {
            throw new \Exception('Parameter to Status requires to be provided');
        }
        return $this->resolveIssue(
            $cylModels,
            $documentNumber,
            $evidenceDocument,
            $fromLocation,
            $toLocation,
            $toStatus,
            $notes,
            $user,
            $metadata,
        );
    }

    public function takeForRefill(
        /** @param App\Models\GasCylinder */
        array $cylModels,
        ?string $documentNumber,
        ?string $evidenceDocument,
        ?GasLocationModel $fromLocation,
        ?GasLocationModel $toLocation,
        string $notes,
        UserModel $user,
        array $metadata,
    ): GasTransactionHeader {
        if (!$fromLocation) {
            throw new \Exception('Parameter from location requires to be provided');
        }
        if (!$toLocation) {
            throw new \Exception('Parameter to location requires to be provided');
        }
        return $this->refillService->takeForRefill(
            $cylModels,
            $documentNumber,
            $evidenceDocument,
            $fromLocation,
            $toLocation,
            $notes,
            $user,
            $metadata
        );
    }

    public function returnFromRefill(
        /** @param App\Models\GasCylinder */
        array $cylModels,
        ?string $documentNumber,
        ?string $evidenceDocument,
        ?GasLocationModel $fromLocation,
        ?GasLocationModel $toLocation,
        string $notes,
        UserModel $user,
        array $metadata,
    ): GasTransactionHeader {
        if (!$fromLocation) {
            throw new \Exception('Parameter from location requires to be provided');
        }
        if (!$toLocation) {
            throw new \Exception('Parameter to location requires to be provided');
        }

        return $this->refillService->returnFromRefill(
            $cylModels,
            $documentNumber,
            $evidenceDocument,
            $fromLocation,
            $toLocation,
            $notes,
            $user,
            $metadata
        );
    }
}
