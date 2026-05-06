<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone;

use Khakimjanovich\UzPhone\Enum\ValidationError;

final readonly class ParseResult
{
    /**
     * @param list<ValidationError> $errors
     */
    private function __construct(
        private ?PhoneNumber $phoneNumber,
        private array $errors,
    ) {
    }

    public static function valid(PhoneNumber $phoneNumber): self
    {
        return new self($phoneNumber, []);
    }

    public static function invalid(ValidationError $error): self
    {
        return new self(null, [$error]);
    }

    public function isValid(): bool
    {
        return $this->phoneNumber !== null;
    }

    public function phoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    /**
     * @return list<ValidationError>
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
