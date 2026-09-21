<?php

namespace App;

enum AlertType: string
{
    case EXPIRING_SOON = 'expiring soon';
    case EXPIRED      = 'expired';
    case LOW_STOCK     = 'low stock';
    case OUT_OF_STOCK    = 'out of stock';
}
