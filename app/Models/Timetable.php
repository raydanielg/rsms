<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timetable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type','name','effective_date','scope_type','scope_id','scope_name','exam_id','status','created_by'
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(TimetableItem::class);
    }
}
