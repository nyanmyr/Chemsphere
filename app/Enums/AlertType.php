<?php

namespace App\Enums;

enum AlertType: string
{
    case EXPIRING_SOON = 'expiring soon';
    case EXPIRED      = 'expired';
    case LOW_STOCK     = 'low stock';
    case OUT_OF_STOCK    = 'out of stock';
    case WARRANTY_EXPIRED = 'warranty expired';
    case WARRANTY_EXPIRING_SOON = 'warranty expiring soon';
}
