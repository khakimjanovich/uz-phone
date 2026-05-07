<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone\Enum;

enum Operator: string
{
    case BEELINE_UNITEL = 'beeline_unitel';
    case BUZTON = 'buzton';
    case EAST_TELECOM = 'east_telecom';
    case EVO_SUPER_IMAX = 'evo_super_imax';
    case HUMANS = 'humans';
    case MOBIUZ_UMS = 'mobiuz_ums';
    case OQ_BEELINE = 'oq_beeline';
    case PERFECTUM_MOBILE = 'perfectum_mobile';
    case SARKOR_TELECOM = 'sarkor_telecom';
    case SHARQ_TELECOM = 'sharq_telecom';
    case TASHKENT_TELEPHONE_NETWORK = 'tashkent_telephone_network';
    case UCELL_COSCOM = 'ucell_coscom';
    case UZTELECOM = 'uztelecom';
    case UZMOBILE_CDMA = 'uzmobile_cdma';
    case UZMOBILE_GSM = 'uzmobile_gsm';

    public function label(): string
    {
        return match ($this) {
            self::BEELINE_UNITEL => 'Beeline (Unitel)',
            self::BUZTON => 'Buzton',
            self::EAST_TELECOM => 'East Telecom',
            self::EVO_SUPER_IMAX => 'EVO («Super Imax»)',
            self::HUMANS => 'Humans',
            self::MOBIUZ_UMS => 'MOBIUZ (UMS)',
            self::OQ_BEELINE => 'Oq (Beeline)',
            self::PERFECTUM_MOBILE => 'Perfectum Mobile',
            self::SARKOR_TELECOM => 'Sarkor Telecom',
            self::SHARQ_TELECOM => 'Sharq Telecom',
            self::TASHKENT_TELEPHONE_NETWORK => 'Ташкентская телефонная сеть',
            self::UCELL_COSCOM => 'Ucell (Coscom)',
            self::UZTELECOM => 'Узбектелеком',
            self::UZMOBILE_CDMA => 'Uzmobile (CDMA)',
            self::UZMOBILE_GSM => 'Uzmobile (GSM)',
        };
    }

    public function isMobile(): bool
    {
        return match ($this) {
            self::BEELINE_UNITEL,
            self::HUMANS,
            self::MOBIUZ_UMS,
            self::OQ_BEELINE,
            self::PERFECTUM_MOBILE,
            self::UCELL_COSCOM,
            self::UZMOBILE_CDMA,
            self::UZMOBILE_GSM => true,
            default => false,
        };
    }
}
