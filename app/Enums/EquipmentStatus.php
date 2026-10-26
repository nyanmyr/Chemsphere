<?php

namespace App\Enums;

enum EquipmentStatus : string
{
    case AVAILABLE = 'available';
    case OUT_OF_ORDER = 'out of order';
    case FOR_DISPOSAL = 'for disposal';
    case IN_USE = 'in use';
}
