<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone;

use Brick\PhoneNumber\PhoneNumber as BrickPhoneNumber;
use Brick\PhoneNumber\PhoneNumberFormat as BrickPhoneNumberFormat;
use Brick\PhoneNumber\PhoneNumberParseException;
use JsonSerializable;
use Khakimjanovich\UzPhone\Enum\Format;
use Khakimjanovich\UzPhone\Enum\Operator;
use Khakimjanovich\UzPhone\Enum\ParseErrorType;
use Khakimjanovich\UzPhone\Enum\Prefix;
use Khakimjanovich\UzPhone\Enum\PrefixType;
use Stringable;

/**
 * An Uzbek phone number.
 */
final readonly class UzPhoneNumber implements Stringable, JsonSerializable
{
    private BrickPhoneNumber $phone_number;

    private Prefix $prefix;

    /**
     * Private constructor. Use a factory method to obtain an instance.
     *
     * @throws ParseException
     */
    private function __construct(string $phone_number)
    {
        $phone_number = trim($phone_number);

        if ($phone_number === '') {
            throw new ParseException(ParseErrorType::EMPTY);
        }

        if (preg_match('/^\+?[0-9 ()\-]+$/', $phone_number) !== 1) {
            throw new ParseException(ParseErrorType::INVALID_CHARACTERS);
        }

        try {
            $brick_phone_number = BrickPhoneNumber::parse($phone_number, 'UZ');
        } catch (PhoneNumberParseException $exception) {
            throw ParseException::mapBrickParseException($exception);
        }

        $national_number = $brick_phone_number->getNationalNumber();

        if (strlen($national_number) !== 9) {
            throw new ParseException(ParseErrorType::INVALID_LENGTH);
        }

        $prefix = Prefix::tryFrom(substr($national_number, 0, 2));

        if ($prefix === null) {
            throw new ParseException(ParseErrorType::UNKNOWN_PREFIX);
        }

        if (!$brick_phone_number->isValidNumber()) {
            throw new ParseException(ParseErrorType::INVALID_LENGTH);
        }

        $this->phone_number = $brick_phone_number;
        $this->prefix = $prefix;
    }

    /**
     * Parses a string representation of an Uzbek phone number.
     *
     * @param string $phone_number The phone number to parse.
     *
     * @return UzPhoneNumber
     *
     * @throws ParseException
     */
    public static function parse(string $phone_number): UzPhoneNumber
    {
        return new UzPhoneNumber($phone_number);
    }

    /**
     * Returns the national number of this phone number.
     */
    public function getNationalNumber(): string
    {
        return $this->phone_number->getNationalNumber();
    }

    /**
     * Returns the country code of this phone number.
     */
    public function getCountryCode(): string
    {
        return $this->phone_number->getCountryCode();
    }

    public function getPrefix(): Prefix
    {
        return $this->prefix;
    }

    public function getOperator(): ?Operator
    {
        return $this->prefix->operator();
    }

    public function getPrefixType(): PrefixType
    {
        return $this->prefix->type();
    }

    public function isEqualTo(UzPhoneNumber $phone_number): bool
    {
        return $this->phone_number->isEqualTo($phone_number->phone_number);
    }

    /**
     * Required by interface JsonSerializable.
     */
    public function jsonSerialize(): string
    {
        return (string) $this;
    }

    /**
     * Returns a string representation of this phone number in international E164 format.
     */
    public function __toString(): string
    {
        return $this->format(Format::E164);
    }

    /**
     * Returns a formatted string representation of this phone number.
     */
    public function format(Format $format): string
    {
        return match ($format) {
            Format::E164 => $this->phone_number->format(BrickPhoneNumberFormat::E164),
            Format::INTERNATIONAL => $this->phone_number->format(BrickPhoneNumberFormat::INTERNATIONAL),
            Format::NATIONAL => $this->phone_number->format(BrickPhoneNumberFormat::NATIONAL),
            Format::MASKED => $this->formatMasked(),
        };
    }

    private function formatMasked(): string
    {
        $national_number = $this->getNationalNumber();
        $prefix = substr($national_number, 0, 2);
        $last_digits = substr($national_number, -2);

        if (strlen($prefix) !== 2 || strlen($last_digits) !== 2) {
            throw new ParseException(ParseErrorType::INVALID_LENGTH);
        }

        return sprintf(
            '+%s %s *** ** %s',
            $this->getCountryCode(),
            $prefix,
            $last_digits,
        );
    }
}
