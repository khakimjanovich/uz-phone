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
        return new self(self::mapBrickParseErrorType($exception->errorType->value));
    }

    private static function mapBrickParseErrorType(int $brick_error_type): ParseErrorType
    {
        return match ($brick_error_type) {
            PhoneNumberParseErrorType::INVALID_COUNTRY_CODE->value => ParseErrorType::INVALID_COUNTRY_CODE,
            PhoneNumberParseErrorType::NOT_A_NUMBER->value => ParseErrorType::INVALID_CHARACTERS,
            PhoneNumberParseErrorType::TOO_SHORT_AFTER_IDD->value,
            PhoneNumberParseErrorType::TOO_SHORT_NSN->value,
            PhoneNumberParseErrorType::TOO_LONG->value => ParseErrorType::INVALID_LENGTH,
            default => ParseErrorType::UNHANDLED_BRICK_ERROR,
        };
    }
}
