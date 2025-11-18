<?php

namespace App\Enums;

enum GasCompanyCategory: string
{
    case EXTERNAL = 'external';
    case INTERNAL = 'internal';

    public function label()
    {
        return match($this) {
            self::INTERNAL => 'Internal',
            self::EXTERNAL => 'Vendor External'
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

    public function isInternal()
    {
        return $this === self::INTERNAL;
    }

    public function isExternal()
    {
        return $this === self::EXTERNAL;
    }

}
