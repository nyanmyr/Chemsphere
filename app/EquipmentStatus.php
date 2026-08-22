<?php

namespace App;

enum EquipmentStatus : string
{
    case AVAILABLE = 'available';
    case UNAVAILABLE = 'unavailable';
    case BROKEN = 'broken';
    case UNDER_MAINTENANCE = 'under maintenance';
}
