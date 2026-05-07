<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone;

use Brick\PhoneNumber\PhoneNumberParseErrorType;
use Brick\PhoneNumber\PhoneNumberParseException;
use Exception;
use Khakimjanovich\UzPhone\Enum\ParseErrorType;

final class ParseException extends Exception
{
    public readonly ParseErrorType $error_type;

    public function __construct(ParseErrorType $error_type)
    {
        parent::__construct($error_type->value);

        $this->error_type = $error_type;
    }

    public static function mapBrickParseException(PhoneNumberParseException $exception): ParseException
    {
        $error_type = match ($exception->errorType) {
            PhoneNumberParseErrorType::INVALID_COUNTRY_CODE => ParseErrorType::INVALID_COUNTRY_CODE,
            PhoneNumberParseErrorType::NOT_A_NUMBER => ParseErrorType::INVALID_CHARACTERS,
            PhoneNumberParseErrorType::TOO_SHORT_AFTER_IDD,
            PhoneNumberParseErrorType::TOO_SHORT_NSN,
            PhoneNumberParseErrorType::TOO_LONG => ParseErrorType::INVALID_LENGTH,
            default => ParseErrorType::UNHANDLED_BRICK_ERROR,
        };

        return new self($error_type);
    }
}
