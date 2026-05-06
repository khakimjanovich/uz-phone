<?php

declare(strict_types=1);

use Khakimjanovich\UzPhone\Enum\MobileOperator;
use Khakimjanovich\UzPhone\Enum\MobilePrefix;
use Khakimjanovich\UzPhone\Enum\PhoneNumberType;
use Khakimjanovich\UzPhone\PrefixMetadata;
use Khakimjanovich\UzPhone\UzPhone;

dataset('valid inputs', [
    'e164 compact' => ['+998901234567'],
    'international compact' => ['998901234567'],
    'national compact' => ['901234567'],
    'national spaced' => ['90 123 45 67'],
    'national punctuated' => ['(90) 123-45-67'],
]);

it('normalizes supported input shapes', function (string $input): void {
    expect(UzPhone::isValid($input))->toBeTrue()
        ->and(UzPhone::normalize($input))->toBe('+998901234567');
})->with('valid inputs');

dataset('invalid inputs', [
    'landline prefix' => ['+998711234567'],
    'unknown prefix' => ['+998321234567'],
    'too short' => ['+99890123456'],
    'too long' => ['+9989012345678'],
    'wrong country code' => ['+997901234567'],
    'alphabetic input' => ['+99890abc4567'],
    'double country code' => ['+998998901234567'],
    'plus sign after first character' => ['998+901234567'],
    'unsupported separator' => ['90.123.45.67'],
    'line break separator' => ["90\n1234567"],
]);

it('rejects invalid input', function (string $input): void {
    expect(UzPhone::isValid($input))->toBeFalse()
        ->and(UzPhone::normalize($input))->toBeNull()
        ->and(UzPhone::format($input))->toBeNull()
        ->and(UzPhone::mask($input))->toBeNull()
        ->and(UzPhone::metadata($input))->toBeNull();
})->with('invalid inputs');

it('formats and masks valid numbers', function (): void {
    expect(UzPhone::format('901234567'))->toBe('+998 90 123 45 67')
        ->and(UzPhone::mask('901234567'))->toBe('+998 90 *** ** 67');
});

dataset('prefix metadata', [
    '33 HUMANS' => [MobilePrefix::P33, MobileOperator::Humans],
    '50 UCELL' => [MobilePrefix::P50, MobileOperator::Ucell],
    '77 UZMOBILE' => [MobilePrefix::P77, MobileOperator::Uzmobile],
    '88 MOBIUZ' => [MobilePrefix::P88, MobileOperator::Mobiuz],
    '90 BEELINE' => [MobilePrefix::P90, MobileOperator::Beeline],
    '91 BEELINE' => [MobilePrefix::P91, MobileOperator::Beeline],
    '93 UCELL' => [MobilePrefix::P93, MobileOperator::Ucell],
    '94 UCELL' => [MobilePrefix::P94, MobileOperator::Ucell],
    '95 UZMOBILE' => [MobilePrefix::P95, MobileOperator::Uzmobile],
    '97 MOBIUZ' => [MobilePrefix::P97, MobileOperator::Mobiuz],
    '98 PERFECTUM MOBILE' => [MobilePrefix::P98, MobileOperator::PerfectumMobile],
    '99 UZMOBILE' => [MobilePrefix::P99, MobileOperator::Uzmobile],
]);

it('returns enum metadata for supported mobile prefixes', function (
    MobilePrefix $prefix,
    MobileOperator $operator,
): void {
    $metadata = UzPhone::metadata($prefix->value . '1234567');

    expect($metadata)->toBeInstanceOf(PrefixMetadata::class)
        ->and($metadata?->prefix)->toBe($prefix)
        ->and($metadata?->operator)->toBe($operator)
        ->and($metadata?->type)->toBe(PhoneNumberType::Mobile);
})->with('prefix metadata');
