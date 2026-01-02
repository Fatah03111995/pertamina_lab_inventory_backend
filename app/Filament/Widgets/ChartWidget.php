<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget as BaseChartWidget;
use App\Models\GasEvent;
use App\Models\GasType;
use App\Models\GasCylinder;
use Carbon\Carbon;
use App\Enums\GasEventType;

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
class ChartWidget extends BaseChartWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
    }

    public function getHeading(): ?string
    {
        return 'Penggunaan Tabung per Tipe (per bulan)';
    }


    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $labels = [];
        $now = Carbon::now();
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $dt = $now->copy()->subMonths($i);
            $labels[] = $dt->format('Y-m');
            $months[] = [
                'start' => $dt->copy()->startOfMonth(),
                'end' => $dt->copy()->endOfMonth(),
            ];
        }

        $datasets = [];
        $colors = ['#ef4444','#f59e0b','#10b981','#3b82f6','#8b5cf6','#ec4899','#06b6d4','#84cc16'];
        $types = GasType::all();
        foreach ($types as $index => $type) {
            $data = [];
            foreach ($months as $m) {
                $count = GasEvent::whereBetween('created_at', [$m['start'], $m['end']])
                    ->whereHas('gasCylinder', function ($q) use ($type) {
                        $q->where('gas_type_id', $type->id);
                    })
                    ->whereIn('event_type', [GasEventType::TAKE_FOR_REFILL->value, GasEventType::MARK_EMPTY->value])
                    ->count();
                $data[] = $count;
            }

            $datasets[] = [
                'label' => $type->name ?? ('Tipe ' . $type->id),
                'data' => $data,
                'borderColor' => $colors[$index % count($colors)],
                'backgroundColor' => 'rgba(0,0,0,0)',
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }
}
