<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\GasEvent;
use App\Models\GasType;
use App\Models\GasCylinder;
use Carbon\Carbon;

/**
 * ChartWidget
 *
 * Menyediakan dua chart untuk dashboard:
 * - movement per month (last 12 months)
 * - cylinder type distribution
 *
 * Catatan belajar:
 * - Kita kumpulkan data dalam format yang mudah dipakai oleh Chart.js
 * - Blade view akan memanggil Chart.js untuk merender canvas
 */
class ChartWidget extends Widget
{
    public string $view = 'filament.widgets.chart-widget';

    public array $movementLabels = [];
    public array $movementData = [];

    public array $typeLabels = [];
    public array $typeData = [];

    public function mount(): void
    {
        // Movement per month (last 12 months)
        $labels = [];
        $data = [];
        $now = Carbon::now();
        for ($i = 11; $i >= 0; $i--) {
            $dt = $now->copy()->subMonths($i);
            $label = $dt->format('Y-m');
            $labels[] = $label;
            $start = $dt->copy()->startOfMonth();
            $end = $dt->copy()->endOfMonth();
            $count = GasEvent::whereBetween('created_at', [$start, $end])->count();
            $data[] = $count;
        }
        $this->movementLabels = $labels;
        $this->movementData = $data;

        // Cylinder type distribution
        $typeLabels = [];
        $typeData = [];
        foreach (GasType::all() as $type) {
            $typeLabels[] = $type->name ?? ($type->label ?? 'Tipe ' . $type->id);
            $typeData[] = GasCylinder::where('gas_type_id', $type->id)->count();
        }
        $this->typeLabels = $typeLabels;
        $this->typeData = $typeData;
    }
}
