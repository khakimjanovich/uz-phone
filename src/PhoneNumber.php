<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone;

use Khakimjanovich\UzPhone\Enum\MobileOperator;
use Khakimjanovich\UzPhone\Enum\Prefix;
use Khakimjanovich\UzPhone\Enum\PrefixType;

final readonly class PhoneNumber
{
    public function __construct(
        public string $e164,
        public string $national,
        public Prefix $prefix,
        public MobileOperator $operator,
        public PrefixType $type,
        public string $formatted,
        public string $masked,
    ) {
    }

    public function metadata(): PrefixMetadata
    {
        return new PrefixMetadata($this->prefix, $this->operator, $this->type);
    }
}
