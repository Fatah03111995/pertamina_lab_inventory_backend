<?php

namespace App\Enums;

enum GasEventType: string
{
    case TAKE_FOR_REFILL = 'take_for_refill';
    case RETURN_FROM_REFILL = 'return_from_refill';
    case MOVEMENT_EXTERNAL = 'movement_external';
    case MOVEMENT_INTERNAL = 'movement_internal';
    case MAINTENANCE_START = 'maintenance_start';
    case MAINTENANCE_END = 'maintenance_end';
    case REPORT_LOST = 'report_lost';
    case RESOLVE_ISSUE = 'resolve_issue';
    case MARK_EMPTY = 'mark_empty';

    public function requireEvidenceDocument(): bool
    {
        return in_array($this, [
            self::TAKE_FOR_REFILL,
            self::RETURN_FROM_REFILL,
            self::MOVEMENT_EXTERNAL,
        ]);
    }

    public function label()
    {
        return match($this) {
            self::TAKE_FOR_REFILL => 'Diambil untuk Pengisian',
            self::RETURN_FROM_REFILL => 'Kembali dari Pengisian',
            self::MOVEMENT_EXTERNAL => 'Movement External',
            self::MOVEMENT_INTERNAL => 'Movement Internal',
            self::MAINTENANCE_START => 'Mulai Perbaikan',
            self::MAINTENANCE_END => 'Selesai Perbaikan',
            self::REPORT_LOST => 'Lapor Hilang',
            self::RESOLVE_ISSUE => 'Resolve Issue',
            self::MARK_EMPTY => 'Kosong Setelah Digunakan',
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
}
