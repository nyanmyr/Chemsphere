<?php

namespace App;

enum AuditAction: string
{
    case INSERT = 'INSERT';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE';
}
