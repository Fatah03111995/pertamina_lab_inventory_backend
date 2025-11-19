<?php

namespace App\Enums;

enum GasEventType: string
{
    case TAKE_FOR_REFILL = 'take_for_refill';
    case RETURN_FROM_REFILL = 'return_from_refill';
    case MOVEMENT_EXTERNAL = 'movement_external';
    case MOVEMENT_INTERNAL = 'movement_internal';
    case USING = 'using';

    public function requireTransaction(): bool
    {
        return in_array($this, [
            self::TAKE_FOR_REFILL,
            self::RETURN_FROM_REFILL,
            self::MOVEMENT_EXTERNAL,
        ]);
    }
}
