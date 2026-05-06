<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone\Enum;

enum MobilePrefix: string
{
    case P33 = '33';
    case P50 = '50';
    case P77 = '77';
    case P88 = '88';
    case P90 = '90';
    case P91 = '91';
    case P93 = '93';
    case P94 = '94';
    case P95 = '95';
    case P97 = '97';
    case P98 = '98';
    case P99 = '99';

    public function operator(): MobileOperator
    {
        return match ($this) {
            self::P33 => MobileOperator::Humans,
            self::P50, self::P93, self::P94 => MobileOperator::Ucell,
            self::P77, self::P95, self::P99 => MobileOperator::Uzmobile,
            self::P88, self::P97 => MobileOperator::Mobiuz,
            self::P90, self::P91 => MobileOperator::Beeline,
            self::P98 => MobileOperator::PerfectumMobile,
        };
    }
}
