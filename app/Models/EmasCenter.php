<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmasCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'center_name',
        'center_code',
        'address',
        'district',
        'region',
        'phone',
        'email',
        'capacity',
        'coordinator_name',
        'coordinator_phone',
        'status',
    ];

    public function candidates(): HasMany
    {
        return $this->hasMany(EmasCandidate::class, 'exam_center_code', 'center_code');
    }

    public function activeCandidates(): HasMany
    {
        return $this->candidates()->where('status', 'active');
    }
}
