<?php

namespace App\Enums;

enum GasTransactionType: string
{
    case MOVEMENT = 'movement';
    case MARK_EMPTY = 'mark_empty';
    case TAKE_FOR_REFILL = 'take_for_refill';
    case RETURN_FROM_REFILL = 'return_from_refill';
    case MAINTENANCE_START = 'maintenance_start';
    case MAINTENANCE_END = 'maintenance_end';
    case REPORT_LOST = 'report_lost';
    case RESOLVE_ISSUE = 'resolve_issue';

    public function requireEvidenceDocument(): bool
    {
        return in_array($this, [
            self::TAKE_FOR_REFILL,
            self::RETURN_FROM_REFILL,
            self::MAINTENANCE_START,
            self::MAINTENANCE_END,
        ]);
    }

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
