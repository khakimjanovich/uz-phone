<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone\Enum;

enum PrefixType: string
{
    case Mobile = 'mobile';
    case FixedLine = 'fixed_line';
}
