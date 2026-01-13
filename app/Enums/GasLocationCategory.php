<?php

namespace App\Enums;

enum GasLocationCategory: string
{
    case STORAGE = 'storage';
    case CONSUMPTION = 'consumption';
    case MAINTENANCE = 'maintenance';
    case REFILLING = 'refilling';

    public function label()
    {
        return ucwords(str_replace('_', ' ', $this->value));
    }

    public static function labels()
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->label();
        }
        return $out;
    }

    public function isStorage(): bool
    {
        return $this === self::STORAGE;
    }

    public function isConsumption(): bool
    {
        return $this === self::CONSUMPTION;
    }

    public function isMaintenance(): bool
    {
        return $this === self::MAINTENANCE;
    }

    public function isRefilling(): bool
    {
        return $this === self::REFILLING;
    }

}
