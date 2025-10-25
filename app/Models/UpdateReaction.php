<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdateReaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'update_id','user_id','reaction' // reaction: accepted|rejected
    ];

    public function updateEntry()
    {
        return $this->belongsTo(Update::class, 'update_id');
    }
}
