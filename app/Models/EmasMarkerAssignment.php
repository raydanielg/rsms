<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmasMarkerAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'marker_id',
        'exam_id',
        'papers_assigned',
        'papers_marked',
        'papers_pending',
        'status',
        'assigned_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function marker(): BelongsTo
    {
        return $this->belongsTo(EmasMarker::class, 'marker_id');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(EmasExam::class, 'exam_id');
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->papers_assigned == 0) {
            return 0;
        }
        
        return round(($this->papers_marked / $this->papers_assigned) * 100, 2);
    }
}
