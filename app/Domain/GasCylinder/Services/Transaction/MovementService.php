<?php

namespace App\Domain\GasCylinder\Services\Transaction;

use App\Domain\GasCylinder\Policies\Transaction\GasTransactionPolicies;
use App\Enums\GasCylinderStatus;
use App\Enums\GasTransactionType;
use App\Models\GasLocation as GasLocationModel;
use App\Models\User as UserModel;
use App\Models\GasTransactionHeader;
use Illuminate\Support\Facades\DB;

class MovementService extends BaseTransactionService
{
    public function __construct(
        private readonly GasTransactionPolicies $policies
    ) {
    }
    public function movement(
        /** @param App\Models\GasCylinder */
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
        return DB::transaction(
            function () use (
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
                //Header
                $header = GasTransactionHeader::create([
                    'transaction_type' => GasTransactionType::MOVEMENT->value,
                    'document_number' => $documentNumber ?? null,
                    'evidence_document' => $evidenceDocument ?? null,
                    'from_location_id' => $fromLocation->id,
                    'to_location_id' => $toLocation->id,
                    'notes' => $notes,
                    'created_by' => $user->id
                ]);

                //Transaction
                foreach ($cylModels as $cylModel) {
                    $cylModel->refresh();
                    $transactionType = $header->transaction_type;
                    $fromStatus = $cylModel->status;

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
                        $metadata,
                    );
                }
                return $header;
            }
        );
    }

    public function markEmpty(
        /**
         * @param App\Models\GasCylinder[] $cylModels
         */
        array $cylModels,
        ?string $documentNumber,
        ?string $evidenceDocument,
        string $notes,
        UserModel $user,
        array $metadata,
    ): GasTransactionHeader {
        return DB::transaction(function () use (
            $cylModels,
            $documentNumber,
            $evidenceDocument,
            $notes,
            $user,
            $metadata,
        ) {
            // Create Header
            $header = GasTransactionHeader::create([
                'transaction_type' => GasTransactionType::MARK_EMPTY->value,
                'document_number' => $documentNumber ?? null,
                'evidence_document' => $evidenceDocument ?? null,
                'from_location_id' => null,
                'to_location_id' => null,
                'notes' => $notes,
                'created_by' => $user->id
            ]);

            foreach ($cylModels as $cylModel) {
                $cylModel->refresh();

                $transactionType = GasTransactionType::from($header->transaction_type);
                $fromLocation = GasLocationModel::findOrFail($cylModel->current_location_id);
                $toLocation = GasLocationModel::findOrFail($cylModel->current_location_id);
                $fromStatus = GasCylinderStatus::from($cylModel->status);
                $toStatus = GasCylinderStatus::EMPTY;

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
