<?php

namespace App\Models;

use App\Models\Alert;
use App\Enums\UserRole;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, BroadcastsEvents;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'email',
        'password',
        'google_id',
        'user_role'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_role' => UserRole::class
        ];
    }

    public function alerts(): MorphMany
    {
        return $this->morphMany(Alert::class, 'notifiable');
    }

    public function scopeReceivesAlerts($query)
    {
        return $query->whereIn('user_role', [UserRole::USER, UserRole::ADMIN]);
    }

    public function broadcastOn(string $event): array
    {
        return [new PrivateChannel('users')];
    }
}
