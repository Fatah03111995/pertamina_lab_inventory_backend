<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\GasCompany;
use App\Models\GasLocation;
use App\Models\GasCylinder;
use App\Models\GasEvent;
use App\Models\GasType;
use Illuminate\Support\Facades\DB;

/**
 * CountWidget
 *
 * Widget ini menampilkan beberapa angka utama (counts) untuk dashboard Filament:
 * - total companies
 * - total locations
 * - total cylinders
 * - total events
 * - total cylinder per type
 *
 * Catatan untuk belajar:
 * - Mount method mengambil data dari model Eloquent.
 * - Untuk menampilkan breakdown per type, kita mengambil semua `GasType`
 *   lalu menghitung jumlah `GasCylinder` per tipe.
 */
class CountWidget extends Widget
{
    public string $view = 'filament.widgets.count-widget';

    public int $companies = 0;
    public int $locations = 0;
    public int $cylinders = 0;
    public int $events = 0;
    public array $cylindersPerType = [];

    public function mount(): void
    {
        $this->companies = GasCompany::count();
        $this->locations = GasLocation::count();
        $this->cylinders = GasCylinder::count();
        $this->events = GasEvent::count();

        // Breakdown per gas type: simple approach for clarity (not optimized)
        $this->cylindersPerType = [];
        foreach (GasType::all() as $type) {
            $this->cylindersPerType[] = [
                'name' => $type->name ?? ($type->label ?? 'Tipe ' . $type->id),
                'count' => GasCylinder::where('gas_type_id', $type->id)->count(),
            ];
        }
    }
}
