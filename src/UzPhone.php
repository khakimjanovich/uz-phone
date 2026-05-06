<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone;

use Brick\PhoneNumber\PhoneNumber as BrickPhoneNumber;
use Brick\PhoneNumber\PhoneNumberFormat;
use Brick\PhoneNumber\PhoneNumberParseErrorType;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberType as BrickPhoneNumberType;
use Khakimjanovich\UzPhone\Enum\Prefix;
use Khakimjanovich\UzPhone\Enum\PrefixType;
use Khakimjanovich\UzPhone\Enum\ValidationError;

final class UzPhone
{
    public const COUNTRY_CODE = '998';

    public static function parse(string $input): ParseResult
    {
        $parsed = self::parseBrickPhoneNumber($input);

        if ($parsed instanceof ValidationError) {
            return ParseResult::invalid($parsed);
        }

        $nationalNumber = $parsed->getNationalNumber();
        $prefix = Prefix::from(substr($nationalNumber, 0, 2));
        $operator = $prefix->operator();

        if ($operator === null) {
            return ParseResult::invalid(ValidationError::NotMobile);
        }

        $phoneNumber = new PhoneNumber(
            e164: $parsed->format(PhoneNumberFormat::E164),
            national: $nationalNumber,
            prefix: $prefix,
            operator: $operator,
            type: PrefixType::Mobile,
            formatted: $parsed->format(PhoneNumberFormat::INTERNATIONAL),
            masked: self::maskNationalNumber($nationalNumber),
        );

        return ParseResult::valid($phoneNumber);
    }

    private static function maskNationalNumber(string $nationalNumber): string
    {
        return sprintf(
            '+%s %s *** ** %s',
            self::COUNTRY_CODE,
            substr($nationalNumber, 0, 2),
            substr($nationalNumber, 7, 2),
        );
    }

    private static function parseBrickPhoneNumber(string $input): BrickPhoneNumber|ValidationError
    {
        $input = trim($input);

        if ($input === '') {
            return ValidationError::Empty;
        }

        if (substr_count($input, '+') > 1 || (str_contains($input, '+') && !str_starts_with($input, '+'))) {
            return ValidationError::Malformed;
        }

        if (preg_match('/^\+?[0-9 ()\-]+$/', $input) !== 1) {
            return ValidationError::InvalidCharacters;
        }

        try {
            $phoneNumber = BrickPhoneNumber::parse($input, 'UZ');
        } catch (PhoneNumberParseException $exception) {
            return self::mapBrickParseException($exception);
        }

        $nationalNumber = $phoneNumber->getNationalNumber();
        $prefix = substr($nationalNumber, 0, 2);
        $prefixEnum = Prefix::tryFrom($prefix);

        if ($prefixEnum === null) {
            return ValidationError::UnknownPrefix;
        }

        if ($phoneNumber->getNumberType() === BrickPhoneNumberType::FIXED_LINE || $prefixEnum->type() === PrefixType::FixedLine) {
            return ValidationError::NotMobile;
        }

        if (!$phoneNumber->isValidNumber()) {
            return ValidationError::InvalidLength;
        }

        return $phoneNumber;
    }

    private static function mapBrickParseException(PhoneNumberParseException $exception): ValidationError
    {
        return match ($exception->errorType) {
            PhoneNumberParseErrorType::INVALID_COUNTRY_CODE => ValidationError::InvalidCountryCode,
            PhoneNumberParseErrorType::NOT_A_NUMBER => ValidationError::InvalidCharacters,
            PhoneNumberParseErrorType::TOO_SHORT_AFTER_IDD,
            PhoneNumberParseErrorType::TOO_SHORT_NSN,
            PhoneNumberParseErrorType::TOO_LONG => ValidationError::InvalidLength,
        };
    }

}
