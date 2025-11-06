<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\EmasCenter;

class EmasCoordinator extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'username',
        'password',
        'email',
        'phone',
        'id_number',
        'address',
        'status',
        'last_login',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'last_login' => 'datetime',
    ];

    /**
     * The centers that belong to the coordinator
     */
    public function centers(): BelongsToMany
    {
        return $this->belongsToMany(EmasCenter::class, 'center_coordinator', 'coordinator_id', 'center_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Generate username from name
     */
    public static function generateUsername($name)
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/[^a-z0-9\s]/', '', $name);
        
        $parts = explode(' ', $name);
        
        if (count($parts) >= 2) {
            $username = $parts[0] . substr($parts[count($parts) - 1], 0, 1);
        } else {
            $username = $parts[0];
        }
        
        $originalUsername = $username;
        $counter = 1;
        
        while (self::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }
        
        return $username;
    }

    /**
     * Get primary center
     */
    public function primaryCenter()
    {
        return $this->centers()->wherePivot('role', 'primary')->first();
    }

    /**
     * Get all center codes
     */
    public function centerCodes()
    {
        return $this->centers->pluck('center_code')->toArray();
    }
}
