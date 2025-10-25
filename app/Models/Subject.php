<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'school_number',
    ];

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject');
    }
}
