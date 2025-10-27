<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class EmasUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'emas_users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'role',
        'center_code',
        'district',
        'region',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is examiner
     */
    public function isExaminer(): bool
    {
        return $this->role === 'examiner';
    }

    /**
     * Check if user is coordinator
     */
    public function isCoordinator(): bool
    {
        return $this->role === 'coordinator';
    }

    /**
     * Check if user is supervisor
     */
    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }
}
