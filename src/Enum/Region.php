<?php

declare(strict_types=1);

namespace Khakimjanovich\UzPhone\Enum;

enum Region: string
{
    case ANDIJAN_REGION = 'andijan_region';
    case BUKHARA_REGION = 'bukhara_region';
    case DJIZAK_REGION = 'djizak_region';
    case KASHKADARYA_REGION = 'kashkadarya_region';
    case NAVOI_REGION = 'navoi_region';
    case NAMANGAN_REGION = 'namangan_region';
    case REPUBLIC_OF_KARAKALPAKSTAN = 'republic_of_karakalpakstan';
    case SAMARKAND_REGION = 'samarkand_region';
    case SURKHANDARYA_REGION = 'surkhandarya_region';
    case SYRDARYA_REGION = 'syrdarya_region';
    case TASHKENT = 'tashkent';
    case TASHKENT_REGION = 'tashkent_region';
    case UZBEKISTAN = 'uzbekistan';
    case FERGANA_REGION = 'fergana_region';
    case KHOREZM_REGION = 'khorezm_region';

    public function label(): string
    {
        return match ($this) {
            self::ANDIJAN_REGION => 'Andijan region',
            self::BUKHARA_REGION => 'Bukhara region',
            self::DJIZAK_REGION => 'Djizak region',
            self::KASHKADARYA_REGION => 'Kashkadarya region',
            self::NAVOI_REGION => 'Navoi region',
            self::NAMANGAN_REGION => 'Namangan region',
            self::REPUBLIC_OF_KARAKALPAKSTAN => 'Republic of Karakalpakstan',
            self::SAMARKAND_REGION => 'Samarkand region',
            self::SURKHANDARYA_REGION => 'Surkhandarya region',
            self::SYRDARYA_REGION => 'Syrdarya region',
            self::TASHKENT => 'Tashkent',
            self::TASHKENT_REGION => 'Tashkent region',
            self::UZBEKISTAN => 'Uzbekistan',
            self::FERGANA_REGION => 'Fergana region',
            self::KHOREZM_REGION => 'Khorezm region',
        };
    }

}
