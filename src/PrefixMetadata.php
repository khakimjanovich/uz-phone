<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone;

use Khakimjanovich\UzPhone\Enum\MobileOperator;
use Khakimjanovich\UzPhone\Enum\MobilePrefix;
use Khakimjanovich\UzPhone\Enum\PhoneNumberType;

final readonly class PrefixMetadata
{
    public function __construct(
        public MobilePrefix $prefix,
        public MobileOperator $operator,
        public PhoneNumberType $type,
    ) {
    }
}
