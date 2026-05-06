<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone\Enum;

enum ParseErrorType: string
{
    case EMPTY = 'empty';
    case INVALID_CHARACTERS = 'invalid_characters';
    case INVALID_COUNTRY_CODE = 'invalid_country_code';
    case INVALID_LENGTH = 'invalid_length';
    case UNKNOWN_PREFIX = 'unknown_prefix';
}
