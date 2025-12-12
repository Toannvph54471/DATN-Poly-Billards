<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'details',
        'ip_address'
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to create a log entry
     */
    public static function log($action, $description = null, $details = [])
    {
        return self::create([
            'user_id' => auth()->id(), // May be null if system action or guest
            'action' => $action,
            'description' => $description,
            'details' => $details,
            // 'ip_address' => request()->ip() // Can be added if request context exists
        ]);
    }
}
