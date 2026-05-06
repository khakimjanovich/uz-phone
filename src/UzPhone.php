<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone;

use Khakimjanovich\UzPhone\Enum\MobilePrefix;
use Khakimjanovich\UzPhone\Enum\PhoneNumberType;
use Khakimjanovich\UzPhone\Enum\ValidationError;

final class UzPhone
{
    public const COUNTRY_CODE = '998';

    public static function parse(string $input): ParseResult
    {
        $parsed = self::parseNationalNumber($input);

        if ($parsed instanceof ValidationError) {
            return ParseResult::invalid($parsed);
        }

        $prefix = MobilePrefix::from(substr($parsed, 0, 2));
        $phoneNumber = new PhoneNumber(
            e164: '+' . self::COUNTRY_CODE . $parsed,
            national: $parsed,
            prefix: $prefix,
            operator: $prefix->operator(),
            type: PhoneNumberType::Mobile,
            formatted: self::formatNationalNumber($parsed),
            masked: self::maskNationalNumber($parsed),
        );

        return ParseResult::valid($phoneNumber);
    }

    public static function isValid(string $input): bool
    {
        return self::parse($input)->isValid();
    }

    public static function normalize(string $input): ?string
    {
        return self::parse($input)->phoneNumber()?->e164;
    }

    public static function format(string $input): ?string
    {
        return self::parse($input)->phoneNumber()?->formatted;
    }

    public static function mask(string $input): ?string
    {
        return self::parse($input)->phoneNumber()?->masked;
    }

    public static function metadata(string $input): ?PrefixMetadata
    {
        return self::parse($input)->phoneNumber()?->metadata();
    }

    private static function formatNationalNumber(string $nationalNumber): string
    {
        return sprintf(
            '+%s %s %s %s %s',
            self::COUNTRY_CODE,
            substr($nationalNumber, 0, 2),
            substr($nationalNumber, 2, 3),
            substr($nationalNumber, 5, 2),
            substr($nationalNumber, 7, 2),
        );
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

    private static function parseNationalNumber(string $input): string|ValidationError
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

        $digits = preg_replace('/\D/', '', $input);

        if ($digits === null) {
            return ValidationError::Malformed;
        }

        if (strlen($digits) === 12 && !str_starts_with($digits, self::COUNTRY_CODE)) {
            return ValidationError::InvalidCountryCode;
        }

        if (str_starts_with($digits, self::COUNTRY_CODE)) {
            $digits = substr($digits, 3);
        }

        if (strlen($digits) !== 9) {
            return ValidationError::InvalidLength;
        }

        $prefix = substr($digits, 0, 2);

        if (in_array($prefix, ['61', '62', '65', '66', '67', '69', '70', '71', '72', '73', '74', '75', '76', '79'], true)) {
            return ValidationError::NotMobile;
        }

        if (MobilePrefix::tryFrom($prefix) === null) {
            return ValidationError::UnknownPrefix;
        }

        return $digits;
    }
}
