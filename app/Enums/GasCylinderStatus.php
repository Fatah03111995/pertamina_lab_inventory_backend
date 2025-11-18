<?php

namespace App\Enums;

enum GasCylinderStatus: string
{
    case FILLED = 'filled';
    case EMPTY = 'empty';
    case MAINTENANCE = 'maintenance';
    case LOST = 'lost';

    public function label()
    {
        return match($this) {
            self::FILLED => 'Isi',
            self::EMPTY => 'Kosong',
            self::MAINTENANCE => 'Perawatan',
            self::LOST => 'Hilang/Tidak Diketahui',
        };
    }

    public static function labels()
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->label();
        }
        return $out;
    }

    public function isLost(): bool
    {
        return $this === self::LOST;
    }
}
