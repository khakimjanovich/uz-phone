<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone\Enum;

enum ValidationError: string
{
    case Empty = 'empty';
    case InvalidCharacters = 'invalid_characters';
    case Malformed = 'malformed';
    case InvalidCountryCode = 'invalid_country_code';
    case InvalidLength = 'invalid_length';
    case UnknownPrefix = 'unknown_prefix';
    case NotMobile = 'not_mobile';
}
