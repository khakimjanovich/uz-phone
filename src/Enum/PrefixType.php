<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone\Enum;

enum PrefixType: string
{
    case MOBILE_GSM = 'mobile_gsm';
    case MOBILE_CDMA = 'mobile_cdma';
    case MOBILE_CDMA_GSM = 'mobile_cdma_gsm';
    case GEOGRAPHIC_PSTN = 'geographic_pstn';
    case SIP = 'sip';
    case FIXED_NETWORK_SERVICE_PROVIDER = 'fixed_network_service_provider';

    public function isMobile(): bool
    {
        return match ($this) {
            self::MOBILE_GSM,
            self::MOBILE_CDMA,
            self::MOBILE_CDMA_GSM => true,
            default => false,
        };
    }
}
