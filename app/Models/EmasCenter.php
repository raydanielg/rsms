<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\EmasCoordinator;

class EmasCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'center_name',
        'center_code',
        'address',
        'district',
        'region',
        'ownership',
        'phone',
        'email',
        'capacity',
        'coordinator_name',
        'coordinator_phone',
        'coordinator_username',
        'coordinator_password',
        'status',
    ];

    protected $hidden = [
        'coordinator_password',
    ];

    public function candidates(): HasMany
    {
        return $this->hasMany(EmasCandidate::class, 'exam_center_code', 'center_code');
    }

    public function activeCandidates(): HasMany
    {
        return $this->candidates()->where('status', 'active');
    }

    /**
     * The coordinators that belong to the center
     */
    public function coordinators()
    {
        return $this->belongsToMany(EmasCoordinator::class, 'center_coordinator', 'center_id', 'coordinator_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get primary coordinator
     */
    public function primaryCoordinator()
    {
        return $this->coordinators()->wherePivot('role', 'primary')->first();
    }
}
