<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('inventory', function (User $user) {
    return true;
});
