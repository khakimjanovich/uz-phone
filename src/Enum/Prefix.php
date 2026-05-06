<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone\Enum;

enum Prefix: string
{
    case P33 = '33';
    case P50 = '50';
    case P61 = '61';
    case P62 = '62';
    case P65 = '65';
    case P66 = '66';
    case P67 = '67';
    case P69 = '69';
    case P70 = '70';
    case P71 = '71';
    case P72 = '72';
    case P73 = '73';
    case P74 = '74';
    case P75 = '75';
    case P76 = '76';
    case P77 = '77';
    case P79 = '79';
    case P88 = '88';
    case P90 = '90';
    case P91 = '91';
    case P93 = '93';
    case P94 = '94';
    case P95 = '95';
    case P97 = '97';
    case P98 = '98';
    case P99 = '99';

    public function type(): PrefixType
    {
        return match ($this) {
            self::P33,
            self::P50,
            self::P77,
            self::P88,
            self::P90,
            self::P91,
            self::P93,
            self::P94,
            self::P95,
            self::P97,
            self::P98,
            self::P99 => PrefixType::Mobile,
            self::P61,
            self::P62,
            self::P65,
            self::P66,
            self::P67,
            self::P69,
            self::P70,
            self::P71,
            self::P72,
            self::P73,
            self::P74,
            self::P75,
            self::P76,
            self::P79 => PrefixType::FixedLine,
        };
    }

    public function operator(): ?MobileOperator
    {
        return match ($this) {
            self::P33 => MobileOperator::Humans,
            self::P50, self::P93, self::P94 => MobileOperator::Ucell,
            self::P77, self::P95, self::P99 => MobileOperator::Uzmobile,
            self::P88, self::P97 => MobileOperator::Mobiuz,
            self::P90, self::P91 => MobileOperator::Beeline,
            self::P98 => MobileOperator::PerfectumMobile,
            default => null,
        };
    }
}
