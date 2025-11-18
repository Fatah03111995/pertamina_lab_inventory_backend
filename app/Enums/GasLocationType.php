<?php

namespace App\Enums;

enum GasLocationType: string
{
    case WAREHOUSE = 'warehouse';
    case LABORATORIUM = 'laboratorium';
    case WORKSHOP = 'workshop' ;
    case INSTRUMENT = 'instrument';
    case FIELD_OPERATION = 'field_operation';

    public function label()
    {
        return ucwords(str_replace('_', ' ', $this->value));
    }

    public static function labels(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->label();
        }
        return $out;
    }
}
