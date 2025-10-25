<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'reg_no',
        'name',
        'sex',
        'class_name',
        'dob',
        'photo_path',
        'guardian_name',
        'guardian_phone',
        'guardian_relation',
        'exam_no',
        'academic_year',
        'status',
        'status_reason',
        'status_date',
    ];
}
