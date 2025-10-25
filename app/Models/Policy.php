<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_en','title_sw','content_en','content_sw','published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
