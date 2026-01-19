<?php

namespace App\Domain\GasCylinder\Services\Transaction;

use App\Enums\GasCylinderStatus;
use App\Enums\GasTransactionType;
use App\Exceptions\InvariantViolationException;
use App\Models\GasCylinder as GasCylinderModel;
use App\Models\GasLocation as GasLocationModel;
use App\Models\User as UserModel;
use App\Models\GasTransaction as GasTransactionModel;
use RuntimeException;

class BaseTransactionService
{
    protected function performTransaction(
        GasCylinderModel $cylModel,
        string $headerId,
        GasTransactionType $transactionType,
        GasLocationModel $fromLocation,
        GasLocationModel $toLocation,
        GasCylinderStatus $fromStatus,
        GasCylinderStatus $toStatus,
        string $notes,
        UserModel $user,
        array $metadata,
    ): GasTransactionModel {
        // Create Event
        $transaction = GasTransactionModel::create([
            'gas_cylinder_id' => $cylModel->id,
            'header_id' => $headerId,
            'transaction_type' => $transactionType,
            'from_location_id' => $fromLocation->id,
            'to_location_id' => $toLocation->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'notes' => $notes,
            'created_by' => $user->id,
            'metadata' => $metadata
        ]);

        //Update Gas Cylinder
        $currentCylModelVersion = $cylModel->version;
        $updated = GasCylinderModel::where('id', $cylModel->id)
                    ->where('version', $currentCylModelVersion)
                    ->update([
                        'current_location_id' => $toLocation->id,
                        'status' => $toStatus,
                        'version' => $cylModel->version + 1,
                    ]);

        if ($updated === 0) {
            throw new RuntimeException($cylModel->name . 'is modified by another user');
        }

        if (!$transaction) {
            throw new RuntimeException('Failed to make transaction for ' . $cylModel->name);
        }

        return $transaction;
    }
}
