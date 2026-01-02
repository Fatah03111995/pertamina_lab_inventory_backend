<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\GasCompany;
use App\Models\GasLocation;
use App\Models\GasCylinder;
use App\Models\GasEvent;
use App\Models\GasType;

class CountWidget extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = '2';
    protected function getStats(): array
    {
        $stats = [
            Stat::make('Total Cylinder Owner Companies', GasCompany::count()),
        ];

        // Pertamina owned
        $company = GasCompany::whereRaw('LOWER(name) LIKE ?', ['%pertamina%'])->first();
        $pertaminaCount = $company ? $company->gasCylinders()->count() : 0;
        $stats[] = Stat::make('Cylinder Owned by Pertamina', $pertaminaCount);

        return $stats;
    }
}
