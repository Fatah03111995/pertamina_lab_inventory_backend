<?php

namespace App\Observers;

use App\Models\GasType;
use App\Exceptions\InvariantViolationException;

/**
 * Observer untuk GasType
 *
 * Mencegah penghapusan type jika masih ada silinder yang menggunakan type tersebut.
 */
class GasTypeObserver
{
    public function deleting(GasType $type): void
    {
        if ($type->gasCylinders()->exists()) {
            throw new InvariantViolationException('Tidak bisa menghapus gas type, karena masih ada gas cylinder yang menggunakan type ini.');
        }
    }
}
