<?php

namespace App\Enums;

enum GasTransactionType: string
{
    case MOVEMENT = 'movement';
    case MARK_EMPTY = 'mark_empty';
    case TAKE_FOR_REFILL = 'take_for_refill';
    case RETURN_FROM_REFILL = 'return_from_refill';
    case TAKE_FOR_MAINTENANCE = 'take_for_maintenance';
    case RETURN_FROM_MAINTENANCE = 'return_from_maintenance';
    case REPORT_LOST = 'report_lost';
    case RESOLVE_ISSUE = 'resolve_issue';

    public function requireEvidenceDocument(): bool
    {
        return in_array($this, [
            self::TAKE_FOR_REFILL,
            self::RETURN_FROM_REFILL,
            self::TAKE_FOR_MAINTENANCE,
            self::RETURN_FROM_MAINTENANCE,
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

    public function isMovement(): bool
    {
        return $this === self::MOVEMENT;
    }

    public function isMarkEmpty()
    {
        return $this === self::MARK_EMPTY;
    }

    public function isTakeForRefill()
    {
        return $this === self::TAKE_FOR_REFILL;
    }

    public function isReturnFromRefill()
    {
        return $this === self::RETURN_FROM_REFILL;
    }

    public function isTakeForMaintenance()
    {
        return $this === self::TAKE_FOR_MAINTENANCE;
    }

    public function isReturnFromMaintenance()
    {
        return $this === self::RETURN_FROM_MAINTENANCE;
    }

    public function isReportLoss()
    {
        return $this === self::REPORT_LOST;
    }

    public function isResolveissue()
    {
        return $this === self::RESOLVE_ISSUE;
    }
}
