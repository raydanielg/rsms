<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmasExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_name',
        'exam_code',
        'subject',
        'exam_type',
        'level',
        'class_form',
        'exam_date',
        'start_time',
        'end_time',
        'duration',
        'total_marks',
        'pass_marks',
        'description',
        'instructions',
        'status',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function results(): HasMany
    {
        return $this->hasMany(EmasResult::class, 'exam_id');
    }

    public function candidates()
    {
        return $this->belongsToMany(EmasCandidate::class, 'emas_results', 'exam_id', 'candidate_id')
                    ->withPivot('score', 'grade', 'remarks')
                    ->withTimestamps();
    }
}
