<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmasCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'registration_number',
        'exam_center_code',
        'level',
        'class_form',
        'phone',
        'email',
        'guardian_name',
        'guardian_phone',
        'address',
        'district',
        'region',
        'special_needs',
        'photo',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    protected $appends = ['full_name'];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function results(): HasMany
    {
        return $this->hasMany(EmasResult::class, 'candidate_id');
    }

    public function exams()
    {
        return $this->belongsToMany(EmasExam::class, 'emas_results', 'candidate_id', 'exam_id')
                    ->withPivot('score', 'grade', 'remarks')
                    ->withTimestamps();
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(EmasCenter::class, 'exam_center_code', 'center_code');
    }
}
