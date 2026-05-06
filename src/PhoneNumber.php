<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone;

use Khakimjanovich\UzPhone\Enum\MobileOperator;
use Khakimjanovich\UzPhone\Enum\MobilePrefix;
use Khakimjanovich\UzPhone\Enum\PhoneNumberType;

final readonly class PhoneNumber
{
    public function __construct(
        public string $e164,
        public string $national,
        public MobilePrefix $prefix,
        public MobileOperator $operator,
        public PhoneNumberType $type,
        public string $formatted,
        public string $masked,
    ) {
    }

    public function metadata(): PrefixMetadata
    {
        return new PrefixMetadata($this->prefix, $this->operator, $this->type);
    }
}
