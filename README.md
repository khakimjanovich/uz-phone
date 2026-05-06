# uz-phone

Framework-agnostic PHP library for strict Uzbek mobile phone parsing,
normalization, validation, formatting, masking, and prefix metadata.

## Installation

```bash
composer require khakimjanovich/uz-phone
```

## Usage

```php
use Khakimjanovich\UzPhone\UzPhone;

UzPhone::isValid('(90) 123-45-67'); // true
UzPhone::normalize('90 123 45 67'); // +998901234567
UzPhone::format('901234567');       // +998 90 123 45 67
UzPhone::mask('901234567');         // +998 90 *** ** 67

$metadata = UzPhone::metadata('+998901234567');

echo $metadata?->prefix->value;   // 90
echo $metadata?->operator->value; // BEELINE
echo $metadata?->type->value;     // mobile
```

`PrefixMetadata` uses backed enums for prefix, operator, and number type:

```php
use Khakimjanovich\UzPhone\Enum\MobileOperator;
use Khakimjanovich\UzPhone\Enum\MobilePrefix;
use Khakimjanovich\UzPhone\Enum\PhoneNumberType;
```

## Supported Inputs

Valid Uzbek mobile numbers may be provided as:

```text
+998901234567
998901234567
901234567
90 123 45 67
(90) 123-45-67
```

All valid inputs normalize to E.164:

```text
+998901234567
```

## Invalid Inputs

The library returns `false` or `null` for:

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

## Mobile Prefixes

Prefix metadata is based on Uzbekistan's published numbering plan. It reflects
the original allocation, not a guaranteed current operator after number
portability.

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

Source: [ITU Uzbekistan numbering plan update](https://www.itu.int/dms_pub/itu-t/oth/02/02/T02020000E10002PDFE.pdf).

## Testing

```bash
composer test
```
