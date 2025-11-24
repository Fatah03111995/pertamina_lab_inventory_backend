<?php

namespace App\Enums;

enum GasLocationCategory: string
{
    case STORAGE = 'storage';
    case CONSUMPTION = 'consumption';
    case VENDOR = 'vendor' ;
    case MAINTENANCE = 'maintenance';

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

}
