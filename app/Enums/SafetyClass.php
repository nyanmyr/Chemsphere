<?php

namespace App\Enums;

enum SafetyClass : string
{
    case FLAMMABLE = 'flammable';
    case CORROSIVE = 'corrosive';
    case REACTIVE = 'reactive';
    case TOXIC = 'toxic';
}
