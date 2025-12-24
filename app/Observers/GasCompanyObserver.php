<?php

namespace App\Observers;

use App\Models\GasCompany;
use App\Exceptions\InvariantViolationException;

/**
 * Observer untuk GasCompany
 *
 * Tujuan: menegakkan aturan domain bahwa sebuah GasCompany tidak boleh dihapus
 * jika masih ada GasCylinder yang merujuk sebagai pemilik (owner).
 *
 * Catatan belajar:
 * - Observer `deleting` dipanggil sebelum record dihapus dari DB.
 * - Menempatkan validasi domain di observer memastikan aturan berlaku
 *   di semua jalur (Filament, API, Tinker, dsb.).
 */
class GasCompanyObserver
{
    public function deleting(GasCompany $company): void
    {
        if ($company->gasCylinders()->exists()) {
            throw new InvariantViolationException('Tidak bisa menghapus perusahaan, karena masih ada gas cylinder yang dimiliki oleh perusahaan ini.');
        }
    }
}
