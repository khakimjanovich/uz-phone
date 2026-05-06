<?php

declare(strict_types=1);

use Khakimjanovich\UzPhone\Enum\MobileOperator;
use Khakimjanovich\UzPhone\Enum\Prefix;
use Khakimjanovich\UzPhone\Enum\PrefixType;
use Khakimjanovich\UzPhone\Enum\ValidationError;
use Khakimjanovich\UzPhone\ParseResult;
use Khakimjanovich\UzPhone\PrefixMetadata;
use Khakimjanovich\UzPhone\PhoneNumber;
use Khakimjanovich\UzPhone\UzPhone;

dataset('valid inputs', [
    'e164 compact' => ['+998901234567'],
    'international compact' => ['998901234567'],
    'international spaced country code' => ['+998 90 123 45 67'],
    'national compact' => ['901234567'],
    'national spaced' => ['90 123 45 67'],
    'national punctuated' => ['(90) 123-45-67'],
]);

it('parses supported input shapes', function (string $input): void {
    $result = UzPhone::parse($input);

    expect($result->isValid())->toBeTrue()
        ->and($result->phoneNumber()?->e164)->toBe('+998901234567')
        ->and($result->errors())->toBe([]);
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
    expect(UzPhone::parse($input)->isValid())->toBeFalse();
})->with('invalid inputs');

dataset('prefix metadata', [
    '33 HUMANS' => [Prefix::P33, MobileOperator::Humans],
    '50 UCELL' => [Prefix::P50, MobileOperator::Ucell],
    '77 UZMOBILE' => [Prefix::P77, MobileOperator::Uzmobile],
    '88 MOBIUZ' => [Prefix::P88, MobileOperator::Mobiuz],
    '90 BEELINE' => [Prefix::P90, MobileOperator::Beeline],
    '91 BEELINE' => [Prefix::P91, MobileOperator::Beeline],
    '93 UCELL' => [Prefix::P93, MobileOperator::Ucell],
    '94 UCELL' => [Prefix::P94, MobileOperator::Ucell],
    '95 UZMOBILE' => [Prefix::P95, MobileOperator::Uzmobile],
    '97 MOBIUZ' => [Prefix::P97, MobileOperator::Mobiuz],
    '98 PERFECTUM MOBILE' => [Prefix::P98, MobileOperator::PerfectumMobile],
    '99 UZMOBILE' => [Prefix::P99, MobileOperator::Uzmobile],
]);

it('returns enum metadata for supported mobile prefixes', function (
    Prefix $prefix,
    MobileOperator $operator,
): void {
    $metadata = UzPhone::parse($prefix->value . '1234567')->phoneNumber()?->metadata();

    expect($metadata)->toBeInstanceOf(PrefixMetadata::class)
        ->and($metadata?->prefix)->toBe($prefix)
        ->and($metadata?->operator)->toBe($operator)
        ->and($metadata?->type)->toBe(PrefixType::Mobile);
})->with('prefix metadata');

it('parses valid input into a phone number result', function (): void {
    $result = UzPhone::parse('(90) 123-45-67');
    $phoneNumber = $result->phoneNumber();

    expect($result)->toBeInstanceOf(ParseResult::class)
        ->and($result->isValid())->toBeTrue()
        ->and($result->errors())->toBe([])
        ->and($phoneNumber)->toBeInstanceOf(PhoneNumber::class)
        ->and($phoneNumber?->e164)->toBe('+998901234567')
        ->and($phoneNumber?->national)->toBe('901234567')
        ->and($phoneNumber?->prefix)->toBe(Prefix::P90)
        ->and($phoneNumber?->operator)->toBe(MobileOperator::Beeline)
        ->and($phoneNumber?->type)->toBe(PrefixType::Mobile)
        ->and($phoneNumber?->formatted)->toBe('+998 90 123 45 67')
        ->and($phoneNumber?->masked)->toBe('+998 90 *** ** 67');
});

dataset('parse errors', [
    'empty' => ['', [ValidationError::Empty]],
    'invalid characters' => ['+99890abc4567', [ValidationError::InvalidCharacters]],
    'malformed plus' => ['998+901234567', [ValidationError::Malformed]],
    'wrong country code' => ['+997901234567', [ValidationError::InvalidCountryCode]],
    'too short' => ['+99890123456', [ValidationError::InvalidLength]],
    'unknown prefix' => ['+998321234567', [ValidationError::UnknownPrefix]],
    'landline prefix' => ['+998711234567', [ValidationError::NotMobile]],
]);

it('parses invalid input into validation errors', function (string $input, array $errors): void {
    $result = UzPhone::parse($input);

    expect($result->isValid())->toBeFalse()
        ->and($result->phoneNumber())->toBeNull()
        ->and($result->errors())->toBe($errors);
})->with('parse errors');
