<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmasMarker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'marker_code',
        'subjects',
        'district',
        'region',
        'scope',
        'status',
        'exams_marked',
        'papers_marked',
        'qualifications',
        'notes',
    ];

    protected $casts = [
        'subjects' => 'array',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(EmasMarkerAssignment::class, 'marker_id');
    }

    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(EmasExam::class, 'emas_marker_assignments', 'marker_id', 'exam_id')
                    ->withPivot('papers_assigned', 'papers_marked', 'papers_pending', 'status')
                    ->withTimestamps();
    }

    public function canMarkSubject(string $subject): bool
    {
        return in_array($subject, $this->subjects ?? []);
    }

    public function canMarkInDistrict(string $district): bool
    {
        if ($this->scope === 'region') {
            return true; // Can mark all districts in their region
        }
        
        return $this->district === $district;
    }
}
