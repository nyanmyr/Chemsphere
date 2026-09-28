<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('inventory', function (User $user) {
    return true;
});

Broadcast::channel('equipment', function (User $user) {
    return true;
});

Broadcast::channel('locations', function (User $user) {
    return true;
});

Broadcast::channel('alerts', function (User $user) {
    return true;
});

Broadcast::channel('users', function (User $user) {
    return true;
});

Broadcast::channel('usage_logs', function (User $user) {
    return true;
});

Broadcast::channel('audit_logs', function (User $user) {
    return true;
});
