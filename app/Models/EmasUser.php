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
        'assigned_subjects',
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
            'assigned_subjects' => 'array',
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

    /**
     * Check if user is marker (marks entry personnel)
     */
    public function isMarker(): bool
    {
        return $this->role === 'marker';
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayName(): string
    {
        return match($this->role) {
            'coordinator' => 'Coordinator',
            'supervisor' => 'Supervisor',
            'examiner' => 'Examiner',
            'marker' => 'Marks Entry Personnel',
            default => 'User',
        };
    }

    /**
     * Check if marker is assigned to a specific subject
     */
    public function canMarkSubject(string $subject): bool
    {
        if (!$this->isMarker()) {
            return false;
        }

        // If no subjects assigned, can mark all subjects
        if (empty($this->assigned_subjects)) {
            return true;
        }

        // Check if subject is in assigned subjects
        return in_array($subject, $this->assigned_subjects);
    }

    /**
     * Get list of assigned subjects
     */
    public function getAssignedSubjects(): array
    {
        return $this->assigned_subjects ?? [];
    }
}
