<?php

namespace App;

enum UserRole : string
{
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';
    case USER = 'user';
    case ADMIN = 'admin';

    public function isAdmin(): bool {
        return $this === self::ADMIN;
    }

    public function isRole(UserRole $role): bool {
        return $this === $role;
    }
}
