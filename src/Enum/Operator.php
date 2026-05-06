<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone\Enum;

enum Operator: string
{
    case BEELINE = 'beeline';
    case HUMANS = 'humans';
    case MOBIUZ = 'mobiuz';
    case OTHER_FIXED_NETWORK_PROVIDERS = 'other_fixed_network_providers';
    case PERFECTUM_MOBILE = 'perfectum_mobile';
    case UCELL = 'ucell';
    case UZTELECOM = 'uztelecom';
    case UZMOBILE = 'uzmobile';

    public function label(): string
    {
        return match ($this) {
            self::BEELINE => 'BEELINE',
            self::HUMANS => 'HUMANS',
            self::MOBIUZ => 'MOBIUZ',
            self::OTHER_FIXED_NETWORK_PROVIDERS => 'Other operators and providers of a fixed network',
            self::PERFECTUM_MOBILE => 'PERFECTUM MOBILE',
            self::UCELL => 'UCELL',
            self::UZTELECOM => 'UZTELECOM',
            self::UZMOBILE => 'UZMOBILE',
        };
    }
}
