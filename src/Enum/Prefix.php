<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone\Enum;

enum Prefix: string
{
    case P33 = '33';
    case P50 = '50';
    case P55 = '55';
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
    case P78 = '78';
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
            self::P97,
            self::P99 => PrefixType::MOBILE_GSM,
            self::P95 => PrefixType::MOBILE_CDMA_GSM,
            self::P98 => PrefixType::MOBILE_CDMA,
            self::P55 => PrefixType::SIP,
            self::P78 => PrefixType::FIXED_NETWORK_SERVICE_PROVIDER,
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
            self::P79 => PrefixType::GEOGRAPHIC_PSTN,
        };
    }

    public function operator(): ?Operator
    {
        return match ($this) {
            self::P33 => Operator::HUMANS,
            self::P50, self::P93, self::P94 => Operator::UCELL,
            self::P55 => Operator::UZTELECOM,
            self::P77, self::P95, self::P99 => Operator::UZMOBILE,
            self::P78 => Operator::OTHER_FIXED_NETWORK_PROVIDERS,
            self::P88, self::P97 => Operator::MOBIUZ,
            self::P90, self::P91 => Operator::BEELINE,
            self::P98 => Operator::PERFECTUM_MOBILE,
            default => null,
        };
    }
}
