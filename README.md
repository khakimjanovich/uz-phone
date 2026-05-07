# uz-phone

[![Latest Version](https://img.shields.io/packagist/v/khakimjanovich/uz-phone.svg?style=flat-square)](https://packagist.org/packages/khakimjanovich/uz-phone)
[![PHP Version](https://img.shields.io/packagist/php-v/khakimjanovich/uz-phone.svg?style=flat-square)](https://packagist.org/packages/khakimjanovich/uz-phone)
[![License](https://img.shields.io/packagist/l/khakimjanovich/uz-phone.svg?style=flat-square)](LICENSE)
[![Tests](https://img.shields.io/github/actions/workflow/status/khakimjanovich/uz-phone/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/khakimjanovich/uz-phone/actions/workflows/tests.yml)
[![Tests](https://img.shields.io/badge/tests-Pest-cc3e44.svg?style=flat-square)](tests/UzPhoneNumberTest.php)

Strict Uzbek phone parsing for PHP. `UzPhoneNumber` wraps `brick/phonenumber`
internally, adds Uzbekistan prefix data, and exposes a small Brick-style
object API without leaking Brick classes.

```php
use Khakimjanovich\UzPhone\Enum\Format;
use Khakimjanovich\UzPhone\UzPhoneNumber;

$phone_number = UzPhoneNumber::parse('(90) 123-45-67');

echo $phone_number;                         // +998901234567
echo $phone_number->format(Format::MASKED); // +998 90 *** ** 67
```

## Why

Most apps only need one thing from Uzbek phone input: parse it into a canonical
object, identify its prefix type, and then decide what their own domain should
accept. `uz-phone` keeps that surface small, strict, and framework-agnostic.

- Parses Uzbek phone numbers
- Normalizes to E.164: `+998901234567`
- Formats display output: `+998 90 123 45 67`
- Masks private output: `+998 90 *** ** 67`
- Returns prefix data with PHP backed enums
- Uses `brick/phonenumber` for phone-number parsing and formatting
- Keeps Uzbek prefix type and operator data in this package

## Installation

```bash
composer require khakimjanovich/uz-phone
```

Requires PHP 8.2 or newer.

## Usage

```php
<?php

use Khakimjanovich\UzPhone\Enum\Format;
use Khakimjanovich\UzPhone\ParseException;
use Khakimjanovich\UzPhone\UzPhoneNumber;

try {
    $phone_number = UzPhoneNumber::parse('90 123 45 67');
} catch (ParseException $exception) {
    echo $exception->error_type->value;
}

$phone_number->getCountryCode();    // 998
$phone_number->getNationalNumber(); // 901234567
$phone_number->format(Format::E164);          // +998901234567
$phone_number->format(Format::INTERNATIONAL); // +998 90 123 45 67
$phone_number->format(Format::NATIONAL);      // 90 123 45 67
$phone_number->format(Format::MASKED);        // +998 90 *** ** 67
```

## Prefix Data

```php
<?php

use Khakimjanovich\UzPhone\Enum\Operator;
use Khakimjanovich\UzPhone\Enum\Prefix;
use Khakimjanovich\UzPhone\Enum\PrefixType;
use Khakimjanovich\UzPhone\UzPhoneNumber;

$phone_number = UzPhoneNumber::parse('+998901234567');

$phone_number->getPrefix() === Prefix::P90;               // true
$phone_number->getOperator() === Operator::BEELINE;        // true
$phone_number->getPrefixType() === PrefixType::MOBILE_GSM; // true
$phone_number->getOperator()?->value;                      // beeline
$phone_number->getOperator()?->label();                     // BEELINE
```

Prefix data reflects original numbering allocation, not a guaranteed
current operator after number portability. Geographic PSTN prefixes return
`null` from `getOperator()` because the ITU table names the region, not a
specific operator.

## Supported Input

All accepted input normalizes to `+998901234567`.

```text
+998901234567
998901234567
+998 90 123 45 67
901234567
90 123 45 67
(90) 123-45-67
```

The parser is intentionally strict. It accepts digits, a leading `+`, spaces,
parentheses, and hyphens. It rejects unknown prefixes, wrong country codes,
unsupported separators, letters, overlong numbers, and incomplete numbers.

## Uzbek Prefixes

Source: [ITU Uzbekistan numbering plan update](https://www.itu.int/dms_pub/itu-t/oth/02/02/T02020000E10002PDFE.pdf).

| Prefix | Type | Operator |
| --- | --- | --- |
| 33 | `MOBILE_GSM` | `HUMANS` |
| 50 | `MOBILE_GSM` | `UCELL` |
| 55 | `SIP` | `UZTELECOM` |
| 61 | `GEOGRAPHIC_PSTN` |  |
| 62 | `GEOGRAPHIC_PSTN` |  |
| 65 | `GEOGRAPHIC_PSTN` |  |
| 66 | `GEOGRAPHIC_PSTN` |  |
| 67 | `GEOGRAPHIC_PSTN` |  |
| 69 | `GEOGRAPHIC_PSTN` |  |
| 70 | `GEOGRAPHIC_PSTN` |  |
| 71 | `GEOGRAPHIC_PSTN` |  |
| 72 | `GEOGRAPHIC_PSTN` |  |
| 73 | `GEOGRAPHIC_PSTN` |  |
| 74 | `GEOGRAPHIC_PSTN` |  |
| 75 | `GEOGRAPHIC_PSTN` |  |
| 76 | `GEOGRAPHIC_PSTN` |  |
| 77 | `MOBILE_GSM` | `UZMOBILE` |
| 78 | `FIXED_NETWORK_SERVICE_PROVIDER` | `OTHER_FIXED_NETWORK_PROVIDERS` |
| 79 | `GEOGRAPHIC_PSTN` |  |
| 88 | `MOBILE_GSM` | `MOBIUZ` |
| 90 | `MOBILE_GSM` | `BEELINE` |
| 91 | `MOBILE_GSM` | `BEELINE` |
| 93 | `MOBILE_GSM` | `UCELL` |
| 94 | `MOBILE_GSM` | `UCELL` |
| 95 | `MOBILE_CDMA_GSM` | `UZMOBILE` |
| 97 | `MOBILE_GSM` | `MOBIUZ` |
| 98 | `MOBILE_CDMA` | `PERFECTUM_MOBILE` |
| 99 | `MOBILE_GSM` | `UZMOBILE` |

## API

```php
UzPhoneNumber::parse(string $phone_number): UzPhoneNumber
```

`parse()` throws `ParseException` when the input is not a valid
Uzbek phone number. The exception exposes `ParseErrorType` through
`$exception->error_type`.

`UzPhoneNumber` methods:

```php
$phone_number->getCountryCode(): string
$phone_number->getNationalNumber(): string
$phone_number->getPrefix(): Prefix
$phone_number->getOperator(): ?Operator
$phone_number->getPrefixType(): PrefixType
$phone_number->format(Format $format): string
$phone_number->isEqualTo(UzPhoneNumber $phone_number): bool
```

Parse error types:

```php
ParseErrorType::EMPTY
ParseErrorType::INVALID_CHARACTERS
ParseErrorType::INVALID_COUNTRY_CODE
ParseErrorType::INVALID_LENGTH
ParseErrorType::UNKNOWN_PREFIX
ParseErrorType::UNHANDLED_BRICK_ERROR
```

## Testing

```bash
composer test
```

## License

MIT.
