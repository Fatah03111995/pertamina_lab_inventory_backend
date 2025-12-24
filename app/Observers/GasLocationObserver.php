<?php

namespace App\Observers;

use App\Models\GasLocation;
use App\Exceptions\InvariantViolationException;

/**
 * Observer untuk GasLocation
 *
 * Mencegah penghapusan lokasi jika masih ada silinder yang berada pada lokasi tersebut.
 */
class GasLocationObserver
{
    public function deleting(GasLocation $location): void
    {
        if ($location->gasCylinders()->exists()) {
            throw new InvariantViolationException('Tidak bisa menghapus lokasi, karena masih ada gas cylinder yang berada di lokasi ini.');
        }
    }
}
