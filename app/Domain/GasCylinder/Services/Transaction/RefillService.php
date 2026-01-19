<?php

namespace App\Domain\GasCylinder\Services\Transaction;

use App\Domain\GasCylinder\Policies\Transaction\GasTransactionPolicies;
use App\Enums\GasCylinderStatus;
use App\Enums\GasTransactionType;
use App\Models\GasLocation as GasLocationModel;
use App\Models\User as UserModel;
use App\Models\GasTransactionHeader;
use Illuminate\Support\Facades\DB;

class RefillService extends BaseTransactionService
{
    public function __construct(
        private readonly GasTransactionPolicies $policies
    ) {
    }

    public function takeForRefill(
        /**
         * @param App\Models\GasCylinder[] $cylModels
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
        return DB::transaction(
            function () use (
                $cylModels,
                $documentNumber,
                $evidenceDocument,
                $fromLocation,
                $toLocation,
                $notes,
                $user,
                $metadata,
            ) {
                //Header
                $header = GasTransactionHeader::create([
                    'transaction_type' => GasTransactionType::TAKE_FOR_REFILL->value,
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

                    $transactionType = GasTransactionType::from($header->transaction_type);
                    $fromStatus = GasCylinderStatus::from($cylModel->status);
                    $toStatus = GasCylinderStatus::REFILL_PROCESS;

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

    public function returnFromRefill(
        /**
         * @param App\Models\GasCylinder[] $cylModels
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
        return DB::transaction(
            function () use (
                $cylModels,
                $documentNumber,
                $evidenceDocument,
                $fromLocation,
                $toLocation,
                $notes,
                $user,
                $metadata,
            ) {
                //Header
                $header = GasTransactionHeader::create([
                    'transaction_type' => GasTransactionType::RETURN_FROM_REFILL->value,
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
                    $transactionType = GasTransactionType::from($header->transaction_type);
                    $fromStatus = GasCylinderStatus::from($cylModel->status);
                    $toStatus = GasCylinderStatus::FILLED;

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
}
