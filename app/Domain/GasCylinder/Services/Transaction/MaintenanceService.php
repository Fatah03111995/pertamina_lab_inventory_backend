<?php

namespace App\Domain\GasCylinder\Services\Transaction;

use App\Domain\GasCylinder\Policies\Transaction\GasTransactionPolicies;
use App\Enums\GasCylinderStatus;
use App\Enums\GasTransactionType;
use App\Models\GasLocation as GasLocationModel;
use App\Models\User as UserModel;
use App\Models\GasTransactionHeader;
use Illuminate\Support\Facades\DB;

class MaintenanceService extends BaseTransactionService
{
    public function __construct(
        private readonly GasTransactionPolicies $policies
    ) {
    }
    public function takeForMaintenance(
        /**
         * @param GasCylinderModel[] $cylModels
         */
        array $cylModels,
        ?string $documentNumber,
        ?string $evidenceDocument,
        GasLocationModel $fromLocation,
        GasLocationModel $toLocation,
        string $notes,
        UserModel $user,
        array $metadata,
    ): GasTransactionHeader {

        return DB::transaction(function () use (
            $cylModels,
            $documentNumber,
            $evidenceDocument,
            $fromLocation,
            $toLocation,
            $notes,
            $user,
            $metadata,
        ) {
            // Create Header
            $header = GasTransactionHeader::create([
                'transaction_type' => GasTransactionType::TAKE_FOR_MAINTENANCE->value,
                'document_number' => $documentNumber ?? null,
                'evidence_document' => $evidenceDocument ?? null,
                'from_location_id' => $fromLocation->id,
                'to_location_id' => $toLocation->id,
                'notes' => $notes,
                'created_by' => $user->id
            ]);

            foreach ($cylModels as $cylModel) {
                $cylModel->refresh();

                $transactionType = GasTransactionType::from($header->transaction_type);
                $fromStatus = GasCylinderStatus::from($cylModel->status);
                $toStatus = GasCylinderStatus::MAINTENANCE;

                $this->policies->assert(
                    $cylModel,
                    $documentNumber,
                    $evidenceDocument,
                    $transactionType,
                    $fromLocation,
                    $toLocation,
                    $fromStatus,
                    $toStatus,
                    $notes,
                    $user,
                    $metadata,
                );

                $this->performTransaction(
                    $cylModel,
                    $header->id,
                    $transactionType,
                    $fromLocation,
                    $toLocation,
                    $fromStatus,
                    $toStatus,
                    $notes,
                    $user,
                    $metadata
                );
            }
            return $header;
        });
    }

    public function returnFromMaintenance(
        /**
         * @param GasCylinderModel[] $cylModels
         */
        array $cylModels,
        ?string $documentNumber,
        ?string $evidenceDocument,
        GasLocationModel $fromLocation,
        GasLocationModel $toLocation,
        GasCylinderStatus $toStatus,
        string $notes,
        UserModel $user,
        array $metadata,
    ): GasTransactionHeader {

        return DB::transaction(function () use (
            $cylModels,
            $documentNumber,
            $evidenceDocument,
            $fromLocation,
            $toLocation,
            $toStatus,
            $notes,
            $user,
            $metadata,
        ) {
            // Create Header
            $header = GasTransactionHeader::create([
                'transaction_type' => GasTransactionType::RETURN_FROM_MAINTENANCE->value,
                'document_number' => $documentNumber ?? null,
                'evidence_document' => $evidenceDocument ?? null,
                'from_location_id' => $fromLocation->id,
                'to_location_id' => $toLocation->id,
                'notes' => $notes,
                'created_by' => $user->id
            ]);

            foreach ($cylModels as $cylModel) {
                $cylModel->refresh();

                $transactionType = GasTransactionType::from($header->transaction_type);
                $fromStatus = GasCylinderStatus::from($cylModel->status);

                $this->policies->assert(
                    $cylModel,
                    $documentNumber,
                    $evidenceDocument,
                    $transactionType,
                    $fromLocation,
                    $toLocation,
                    $fromStatus,
                    $toStatus,
                    $notes,
                    $user,
                    $metadata
                );

                $this->performTransaction(
                    $cylModel,
                    $header->id,
                    $transactionType,
                    $fromLocation,
                    $toLocation,
                    $fromStatus,
                    $toStatus,
                    $notes,
                    $user,
                    $metadata
                );
            }
            return $header;
        });
    }
}
