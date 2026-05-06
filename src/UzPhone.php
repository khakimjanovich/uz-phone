<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone;

use Khakimjanovich\UzPhone\Enum\MobilePrefix;
use Khakimjanovich\UzPhone\Enum\PhoneNumberType;

final class UzPhone
{
    public const COUNTRY_CODE = '998';

    public static function isValid(string $input): bool
    {
        return self::parseNationalNumber($input) !== null;
    }

    public static function normalize(string $input): ?string
    {
        $nationalNumber = self::parseNationalNumber($input);

        if ($nationalNumber === null) {
            return null;
        }

        return '+' . self::COUNTRY_CODE . $nationalNumber;
    }

    public static function format(string $input): ?string
    {
        $nationalNumber = self::parseNationalNumber($input);

        if ($nationalNumber === null) {
            return null;
        }

        return sprintf(
            '+%s %s %s %s %s',
            self::COUNTRY_CODE,
            substr($nationalNumber, 0, 2),
            substr($nationalNumber, 2, 3),
            substr($nationalNumber, 5, 2),
            substr($nationalNumber, 7, 2),
        );
    }

    public static function mask(string $input): ?string
    {
        $nationalNumber = self::parseNationalNumber($input);

        if ($nationalNumber === null) {
            return null;
        }

        return sprintf(
            '+%s %s *** ** %s',
            self::COUNTRY_CODE,
            substr($nationalNumber, 0, 2),
            substr($nationalNumber, 7, 2),
        );
    }

    public static function metadata(string $input): ?PrefixMetadata
    {
        $nationalNumber = self::parseNationalNumber($input);

        if ($nationalNumber === null) {
            return null;
        }

        $prefix = MobilePrefix::from(substr($nationalNumber, 0, 2));

        return new PrefixMetadata($prefix, $prefix->operator(), PhoneNumberType::Mobile);
    }

    private static function parseNationalNumber(string $input): ?string
    {
        $input = trim($input);

        if ($input === '') {
            return null;
        }

        if (preg_match('/^\+?[0-9 ()\-]+$/', $input) !== 1) {
            return null;
        }

        if (substr_count($input, '+') > 1 || (str_contains($input, '+') && !str_starts_with($input, '+'))) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $input);

        if ($digits === null) {
            return null;
        }

        if (str_starts_with($digits, self::COUNTRY_CODE)) {
            $digits = substr($digits, 3);
        }

        if (strlen($digits) !== 9) {
            return null;
        }

        $prefix = substr($digits, 0, 2);

        if (MobilePrefix::tryFrom($prefix) === null) {
            return null;
        }

        return $digits;
    }
}
