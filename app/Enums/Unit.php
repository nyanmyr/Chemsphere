<?php

namespace App\Enums;

enum Unit : string
{
    case KILOGRAM = 'kilogram';
    case GRAM = 'gram';
    case MILLIGRAM = 'milligram';
    case MICROGRAM = 'microgram';
    case LITTER = 'liter';
    case MILLILITER = 'milliliter';
    case MICROLITER = 'microliter';

    public function quantityType(): string
    {
        return match ($this) {
            self::KILOGRAM,
            self::GRAM,
            self::MILLIGRAM,
            self::MICROGRAM => 'weight',
            self::LITTER,
            self::MILLILITER,
            self::MICROLITER => 'volume',
        };
    }
}
