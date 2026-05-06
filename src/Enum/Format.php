<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone\Enum;

enum Format: string
{
    case E164 = 'e164';
    case INTERNATIONAL = 'international';
    case NATIONAL = 'national';
    case MASKED = 'masked';
}
