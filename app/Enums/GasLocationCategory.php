<?php

namespace App\Enums;

enum GasLocationCategory: string
{
    case INTERNAL = 'internal';
    case EXTERNAL = 'external';

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

    public function isInternal()
    {
        return $this === self::INTERNAL;
    }

    public function isExternal()
    {
        return $this === self::EXTERNAL;
    }
}
