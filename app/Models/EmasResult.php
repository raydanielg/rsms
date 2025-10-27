<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmasResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'candidate_id',
        'score',
        'grade',
        'remarks',
        'comments',
        'uploaded_by',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(EmasExam::class, 'exam_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(EmasCandidate::class, 'candidate_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(EmasUser::class, 'uploaded_by');
    }

    public static function calculateGrade(int $score, int $totalMarks): string
    {
        $percentage = ($score / $totalMarks) * 100;

        if ($percentage >= 80) return 'A';
        if ($percentage >= 65) return 'B';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    public static function getRemarks(string $grade): string
    {
        return match($grade) {
            'A' => 'Excellent',
            'B' => 'Very Good',
            'C' => 'Good',
            'D' => 'Satisfactory',
            'F' => 'Fail',
            default => 'N/A',
        };
    }
}
