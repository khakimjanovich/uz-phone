<?php

declare(strict_types=1);

use Khakimjanovich\UzPhone\Enum\Operator;
use Khakimjanovich\UzPhone\Enum\Format;
use Khakimjanovich\UzPhone\Enum\ParseErrorType;
use Khakimjanovich\UzPhone\Enum\Prefix;
use Khakimjanovich\UzPhone\Enum\PrefixType;
use Khakimjanovich\UzPhone\ParseException;
use Khakimjanovich\UzPhone\UzPhoneNumber;

dataset('valid inputs', [
    'e164 compact' => ['+998901234567'],
    'international compact' => ['998901234567'],
    'international spaced country code' => ['+998 90 123 45 67'],
    'national compact' => ['901234567'],
    'national spaced' => ['90 123 45 67'],
    'national punctuated' => ['(90) 123-45-67'],
]);

it('parses supported input shapes', function (string $input): void {
    $phone_number = UzPhoneNumber::parse($input);

    expect($phone_number)->toBeInstanceOf(UzPhoneNumber::class)
        ->and((string) $phone_number)->toBe('+998901234567')
        ->and($phone_number->jsonSerialize())->toBe('+998901234567')
        ->and($phone_number->getCountryCode())->toBe('998')
        ->and($phone_number->getNationalNumber())->toBe('901234567');
})->with('valid inputs');

it('formats and masks valid numbers', function (): void {
    $phone_number = UzPhoneNumber::parse('901234567');

    expect($phone_number->format(Format::E164))->toBe('+998901234567')
        ->and($phone_number->format(Format::INTERNATIONAL))->toBe('+998 90 123 45 67')
        ->and($phone_number->format(Format::NATIONAL))->toBe('90 123 45 67')
        ->and($phone_number->format(Format::MASKED))->toBe('+998 90 *** ** 67');
});

it('compares phone numbers by value', function (): void {
    $phone_number = UzPhoneNumber::parse('901234567');
    $same_phone_number = UzPhoneNumber::parse('+998 90 123 45 67');
    $different_phone_number = UzPhoneNumber::parse('911234567');

    expect($phone_number->isEqualTo($same_phone_number))->toBeTrue()
        ->and($phone_number->isEqualTo($different_phone_number))->toBeFalse();
});

dataset('prefix data', [
    '33 HUMANS mobile GSM' => [Prefix::P33, Operator::HUMANS, PrefixType::MOBILE_GSM],
    '50 UCELL mobile GSM' => [Prefix::P50, Operator::UCELL, PrefixType::MOBILE_GSM],
    '55 UZTELECOM SIP' => [Prefix::P55, Operator::UZTELECOM, PrefixType::SIP],
    '61 Nukus PSTN' => [Prefix::P61, null, PrefixType::GEOGRAPHIC_PSTN],
    '62 Urgench PSTN' => [Prefix::P62, null, PrefixType::GEOGRAPHIC_PSTN],
    '65 Bukhara PSTN' => [Prefix::P65, null, PrefixType::GEOGRAPHIC_PSTN],
    '66 Samarkand PSTN' => [Prefix::P66, null, PrefixType::GEOGRAPHIC_PSTN],
    '67 Gulistan PSTN' => [Prefix::P67, null, PrefixType::GEOGRAPHIC_PSTN],
    '69 Namangan PSTN' => [Prefix::P69, null, PrefixType::GEOGRAPHIC_PSTN],
    '70 Tashkent region PSTN' => [Prefix::P70, null, PrefixType::GEOGRAPHIC_PSTN],
    '71 Tashkent city PSTN' => [Prefix::P71, null, PrefixType::GEOGRAPHIC_PSTN],
    '72 Djizak PSTN' => [Prefix::P72, null, PrefixType::GEOGRAPHIC_PSTN],
    '73 Fergana PSTN' => [Prefix::P73, null, PrefixType::GEOGRAPHIC_PSTN],
    '74 Andijan PSTN' => [Prefix::P74, null, PrefixType::GEOGRAPHIC_PSTN],
    '75 Karshi PSTN' => [Prefix::P75, null, PrefixType::GEOGRAPHIC_PSTN],
    '76 Termez PSTN' => [Prefix::P76, null, PrefixType::GEOGRAPHIC_PSTN],
    '77 UZMOBILE mobile GSM' => [Prefix::P77, Operator::UZMOBILE, PrefixType::MOBILE_GSM],
    '78 fixed network providers' => [Prefix::P78, Operator::OTHER_FIXED_NETWORK_PROVIDERS, PrefixType::FIXED_NETWORK_SERVICE_PROVIDER],
    '79 Navoi PSTN' => [Prefix::P79, null, PrefixType::GEOGRAPHIC_PSTN],
    '88 MOBIUZ mobile GSM' => [Prefix::P88, Operator::MOBIUZ, PrefixType::MOBILE_GSM],
    '90 BEELINE mobile GSM' => [Prefix::P90, Operator::BEELINE, PrefixType::MOBILE_GSM],
    '91 BEELINE mobile GSM' => [Prefix::P91, Operator::BEELINE, PrefixType::MOBILE_GSM],
    '93 UCELL mobile GSM' => [Prefix::P93, Operator::UCELL, PrefixType::MOBILE_GSM],
    '94 UCELL mobile GSM' => [Prefix::P94, Operator::UCELL, PrefixType::MOBILE_GSM],
    '95 UZMOBILE mobile CDMA and GSM' => [Prefix::P95, Operator::UZMOBILE, PrefixType::MOBILE_CDMA_GSM],
    '97 MOBIUZ mobile GSM' => [Prefix::P97, Operator::MOBIUZ, PrefixType::MOBILE_GSM],
    '98 PERFECTUM MOBILE mobile CDMA' => [Prefix::P98, Operator::PERFECTUM_MOBILE, PrefixType::MOBILE_CDMA],
    '99 UZMOBILE mobile GSM' => [Prefix::P99, Operator::UZMOBILE, PrefixType::MOBILE_GSM],
]);

it('returns prefix data for supported prefixes', function (
    Prefix $prefix,
    ?Operator $operator,
    PrefixType $prefix_type,
): void {
    expect($prefix->operator())->toBe($operator)
        ->and($prefix->type())->toBe($prefix_type);
})->with('prefix data');

it('returns prefix data from parsed phone numbers', function (): void {
    $phone_number = UzPhoneNumber::parse('+998711234567');

    expect($phone_number->getPrefix())->toBe(Prefix::P71)
        ->and($phone_number->getOperator())->toBeNull()
        ->and($phone_number->getPrefixType())->toBe(PrefixType::GEOGRAPHIC_PSTN);
});

it('detects mobile prefix types', function (): void {
    expect(PrefixType::MOBILE_GSM->isMobile())->toBeTrue()
        ->and(PrefixType::MOBILE_CDMA->isMobile())->toBeTrue()
        ->and(PrefixType::MOBILE_CDMA_GSM->isMobile())->toBeTrue()
        ->and(PrefixType::GEOGRAPHIC_PSTN->isMobile())->toBeFalse()
        ->and(PrefixType::SIP->isMobile())->toBeFalse()
        ->and(PrefixType::FIXED_NETWORK_SERVICE_PROVIDER->isMobile())->toBeFalse();
});

it('uses consistent machine-readable enum values', function (): void {
    expect(Operator::BEELINE->value)->toBe('beeline')
        ->and(Operator::HUMANS->value)->toBe('humans')
        ->and(Operator::MOBIUZ->value)->toBe('mobiuz')
        ->and(Operator::OTHER_FIXED_NETWORK_PROVIDERS->value)->toBe('other_fixed_network_providers')
        ->and(Operator::PERFECTUM_MOBILE->value)->toBe('perfectum_mobile')
        ->and(Operator::UCELL->value)->toBe('ucell')
        ->and(Operator::UZTELECOM->value)->toBe('uztelecom')
        ->and(Operator::UZMOBILE->value)->toBe('uzmobile');
});

it('returns operator display labels from the ITU table', function (): void {
    expect(Operator::BEELINE->label())->toBe('BEELINE')
        ->and(Operator::HUMANS->label())->toBe('HUMANS')
        ->and(Operator::MOBIUZ->label())->toBe('MOBIUZ')
        ->and(Operator::OTHER_FIXED_NETWORK_PROVIDERS->label())->toBe('Other operators and providers of a fixed network')
        ->and(Operator::PERFECTUM_MOBILE->label())->toBe('PERFECTUM MOBILE')
        ->and(Operator::UCELL->label())->toBe('UCELL')
        ->and(Operator::UZTELECOM->label())->toBe('UZTELECOM')
        ->and(Operator::UZMOBILE->label())->toBe('UZMOBILE');
});

dataset('parse errors', [
    'empty' => ['', ParseErrorType::EMPTY],
    'invalid characters' => ['+99890abc4567', ParseErrorType::INVALID_CHARACTERS],
    'plus sign after first character' => ['998+901234567', ParseErrorType::INVALID_CHARACTERS],
    'wrong country code' => ['+997901234567', ParseErrorType::INVALID_COUNTRY_CODE],
    'too short' => ['+99890123456', ParseErrorType::INVALID_LENGTH],
    'too long' => ['+9989012345678', ParseErrorType::INVALID_LENGTH],
    'unknown prefix' => ['+998321234567', ParseErrorType::UNKNOWN_PREFIX],
    'unsupported separator' => ['90.123.45.67', ParseErrorType::INVALID_CHARACTERS],
    'line break separator' => ["90\n1234567", ParseErrorType::INVALID_CHARACTERS],
]);

it('throws parse exceptions with package error types', function (
    string $input,
    ParseErrorType $error_type,
): void {
    try {
        UzPhoneNumber::parse($input);
    } catch (ParseException $exception) {
        expect($exception->error_type)->toBe($error_type);

        return;
    }

    $this->fail('Expected ParseException.');
})->with('parse errors');
