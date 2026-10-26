<?php

namespace App\Enums;

enum AlertType: string
{
    case EXPIRING_SOON = 'expiring soon';
    case EXPIRED      = 'expired';
    case LOW_STOCK     = 'low stock';
    case OUT_OF_STOCK    = 'out of stock';
    case MAINTENANCE_CLOSE_TO_DUE = 'maintenance close to due';
    case MAINTENANCE_OVERDUE = 'maintenance overdue';
}
