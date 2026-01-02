<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\CountWidget;
use App\Filament\Widgets\ChartWidget;
use App\Filament\Widgets\StockWidget;
use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;

class CustomDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';

    public function getTitle(): string|Htmlable
    {
        return 'Dashboard';
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 2;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CountWidget::class,
            ChartWidget::class,
            StockWidget::class,
        ];
    }
}
