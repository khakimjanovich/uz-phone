# uz-phone

[![Latest Version](https://img.shields.io/packagist/v/khakimjanovich/uz-phone.svg?style=flat-square)](https://packagist.org/packages/khakimjanovich/uz-phone)
[![PHP Version](https://img.shields.io/packagist/php-v/khakimjanovich/uz-phone.svg?style=flat-square)](https://packagist.org/packages/khakimjanovich/uz-phone)
[![License](https://img.shields.io/packagist/l/khakimjanovich/uz-phone.svg?style=flat-square)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-Pest-cc3e44.svg?style=flat-square)](tests/UzPhoneTest.php)

Strict Uzbek mobile phone parsing for PHP. Normalize messy input into clean
E.164 numbers, validate real Uzbek mobile prefixes, format for display, mask
for privacy, and read prefix metadata through typed enums.

```php
$result = UzPhone::parse('(90) 123-45-67');

$result->phoneNumber()?->e164;      // +998901234567
$result->phoneNumber()?->formatted; // +998 90 123 45 67
$result->phoneNumber()?->masked;    // +998 90 *** ** 67
```

## Why

Most apps only need one thing from Uzbek phone input: decide whether it is a
valid mobile number and store it in one canonical format. `uz-phone` keeps that
surface small, strict, and framework-agnostic.

- Validates Uzbek mobile numbers only
- Normalizes to E.164: `+998901234567`
- Formats display output: `+998 90 123 45 67`
- Masks private output: `+998 90 *** ** 67`
- Returns prefix metadata with PHP backed enums
- Uses `brick/phonenumber` for phone-number parsing and formatting
- Keeps Uzbek mobile validation and operator metadata in this package

## Installation

```bash
composer require khakimjanovich/uz-phone
```

Requires PHP 8.2 or newer.

## How It Works

`uz-phone` uses [`brick/phonenumber`](https://github.com/brick/phonenumber) for
the underlying phone-number parse and format step. On top of that, it applies
strict Uzbekistan-only mobile rules from the ITU numbering plan:

- country code must be `+998`
- national number must be exactly 9 digits
- prefix must be an Uzbek mobile allocation
- fixed-line and unknown prefixes are rejected

This keeps the public API small while avoiding a fully hand-rolled parser.

## Usage

```php
<?php

use Khakimjanovich\UzPhone\UzPhone;

$result = UzPhone::parse('(90) 123-45-67');

$result->isValid();              // true
$result->errors();               // []
$result->phoneNumber()?->e164;   // +998901234567
$result->phoneNumber()?->masked; // +998 90 *** ** 67
```

Invalid input returns `false` from `isValid()` and `null` from value-returning
helpers:

```php
$result = UzPhone::parse('+998711234567');

$result->isValid();     // false
$result->phoneNumber(); // null
$result->errors();      // [ValidationError::NotMobile]
```

## Prefix Metadata

```php
<?php

use Khakimjanovich\UzPhone\Enum\MobileOperator;
use Khakimjanovich\UzPhone\Enum\Prefix;
use Khakimjanovich\UzPhone\Enum\PrefixType;
use Khakimjanovich\UzPhone\UzPhone;

$metadata = UzPhone::parse('+998901234567')->phoneNumber()?->metadata();

$metadata?->prefix === Prefix::P90;                // true
$metadata?->operator === MobileOperator::Beeline;  // true
$metadata?->type === PrefixType::Mobile;           // true

echo $metadata?->prefix->value;   // 90
echo $metadata?->operator->value; // BEELINE
echo $metadata?->type->value;     // mobile
```

Prefix metadata reflects original numbering allocation, not a guaranteed
current operator after number portability. Fixed-line prefixes are modeled in
`Prefix`, but this package rejects them because it validates Uzbek mobile
numbers only.

## Supported Input

All accepted input normalizes to `+998901234567`.

```text
+998901234567
998901234567
901234567
90 123 45 67
(90) 123-45-67
```

The parser is intentionally strict. It accepts digits, a leading `+`, spaces,
parentheses, and hyphens. It rejects unknown prefixes, landlines, wrong country
codes, unsupported separators, letters, overlong numbers, and incomplete
numbers.

```text
+998711234567    landline prefix
+998321234567    unknown mobile prefix
+99890123456     too short
+9989012345678   too long
+997901234567    wrong country code
+99890abc4567    alphabetic input
+998998901234567 double country code
998+901234567    plus sign after first character
90.123.45.67     unsupported separator
90\n1234567      line break separator
```

## Uzbek Mobile Prefixes

Source: [ITU Uzbekistan numbering plan update](https://www.itu.int/dms_pub/itu-t/oth/02/02/T02020000E10002PDFE.pdf).

| Prefix | Operator |
| --- | --- |
| 33 | HUMANS |
| 50 | UCELL |
| 77 | UZMOBILE |
| 88 | MOBIUZ |
| 90 | BEELINE |
| 91 | BEELINE |
| 93 | UCELL |
| 94 | UCELL |
| 95 | UZMOBILE |
| 97 | MOBIUZ |
| 98 | PERFECTUM MOBILE |
| 99 | UZMOBILE |

## API

```php
UzPhone::parse(string $input): ParseResult
```

`ParseResult` is the recommended API for forms and imports:

```php
$result->isValid(): bool
$result->phoneNumber(): ?PhoneNumber
$result->errors(): array
```

`PhoneNumber` exposes normalized values and enum metadata:

```php
$phoneNumber->e164;      // +998901234567
$phoneNumber->national;  // 901234567
$phoneNumber->prefix;    // Prefix::P90
$phoneNumber->operator;  // MobileOperator::Beeline
$phoneNumber->type;      // PrefixType::Mobile
$phoneNumber->formatted; // +998 90 123 45 67
$phoneNumber->masked;    // +998 90 *** ** 67
```

Validation errors are backed enum cases:

```php
ValidationError::Empty
ValidationError::InvalidCharacters
ValidationError::Malformed
ValidationError::InvalidCountryCode
ValidationError::InvalidLength
ValidationError::UnknownPrefix
ValidationError::NotMobile
```

## Testing

```bash
composer test
```

## License

MIT.
