<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone;

use Khakimjanovich\UzPhone\Enum\MobileOperator;
use Khakimjanovich\UzPhone\Enum\Prefix;
use Khakimjanovich\UzPhone\Enum\PrefixType;

final readonly class PrefixMetadata
{
    public function __construct(
        public Prefix $prefix,
        public MobileOperator $operator,
        public PrefixType $type,
    ) {
    }
}
