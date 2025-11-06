<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmasSchool extends Model
{
    use HasFactory;

    protected $table = 'emas_schools';

    protected $fillable = [
        'centre_number',
        'centre_name',
        'region',
        'council',
        'ward',
        'ownership',
        'female_students',
        'male_students',
        'total_students',
        'contact_person',
        'phone',
        'email',
        'address',
        'status',
    ];

    protected $casts = [
        'female_students' => 'integer',
        'male_students' => 'integer',
        'total_students' => 'integer',
    ];

    /**
     * Automatically calculate total students before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($school) {
            $school->total_students = $school->female_students + $school->male_students;
        });
    }

    /**
     * Scope to filter by region
     */
    public function scopeByRegion($query, $region)
    {
        if ($region) {
            return $query->where('region', $region);
        }
        return $query;
    }

    /**
     * Scope to filter by council
     */
    public function scopeByCouncil($query, $council)
    {
        if ($council) {
            return $query->where('council', $council);
        }
        return $query;
    }

    /**
     * Scope to filter by ownership
     */
    public function scopeByOwnership($query, $ownership)
    {
        if ($ownership) {
            return $query->where('ownership', $ownership);
        }
        return $query;
    }

    /**
     * Get schools statistics
     */
    public static function getStatsByRegion($region = null)
    {
        $query = self::query();
        
        if ($region) {
            $query->where('region', $region);
        }

        return [
            'total_schools' => $query->count(),
            'govt_schools' => (clone $query)->where('ownership', 'GOVERNMENT')->count(),
            'private_schools' => (clone $query)->where('ownership', 'PRIVATE')->count(),
            'total_female' => $query->sum('female_students'),
            'total_male' => $query->sum('male_students'),
            'total_students' => $query->sum('total_students'),
        ];
    }
}
