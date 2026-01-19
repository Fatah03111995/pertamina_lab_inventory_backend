<?php

namespace App\Enums;

enum GasCylinderStatus: string
{
    case FILLED = 'filled';
    case IN_USE = 'in_use';
    case REFILL_PROCESS = 'refill_process';
    case EMPTY = 'empty';
    case MAINTENANCE = 'maintenance';
    case LOST = 'lost';

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

    public function isFilled(): bool
    {
        return $this === self::FILLED;
    }

    public function isInUse(): bool
    {
        return $this === self::IN_USE;
    }

    public function isRefillProcess(): bool
    {
        return $this === self::REFILL_PROCESS;
    }

    public function isMaintenance(): bool
    {
        return $this === self::MAINTENANCE;
    }

    public function isEmpty(): bool
    {
        return $this === self::EMPTY;
    }

    public function isLost(): bool
    {
        return $this === self::LOST;
    }

    public function canTransitionTo(GasCylinderStatus $targetStatus): bool
    {
        $allowedTransitions = [
            self::FILLED->value => [
                self::FILLED,
                self::LOST,
                self::IN_USE,
                self::MAINTENANCE,
                self::EMPTY,
            ],
            self::IN_USE->value => [
                self::IN_USE,
                self::EMPTY,
                self::MAINTENANCE,
                self::LOST,
            ],
            self::EMPTY->value => [
                self::EMPTY,
                self::REFILL_PROCESS,
                self::LOST,
            ],
            self::REFILL_PROCESS->value => [
                self::FILLED,
                self::LOST,
            ],
            self::MAINTENANCE->value => [
                self::FILLED,
                self::EMPTY,
                self::LOST,
            ],
            self::LOST->value => [
                self::FILLED,
                self::EMPTY,
                self::MAINTENANCE
            ],
        ];

        $current = $this->value;
        return isset($allowedTransitions[$current])
            && in_array($targetStatus, $allowedTransitions[$current]);
    }
}
