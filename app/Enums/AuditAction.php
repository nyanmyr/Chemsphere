<?php

namespace App\Enums;

enum AuditAction: string
{
    case CREATE = 'CREATE';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE';
    case LOGIN = 'LOGIN';
    case LOGOUT = 'LOGOUT';
    case REGISTER = 'REGISTER';
}
