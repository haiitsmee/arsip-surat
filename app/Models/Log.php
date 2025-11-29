<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'action',
        'letter_type',
        'letter_id',
    ];

    /**
     * Disable updated_at
     */
    const UPDATED_AT = null;

    /**
     * Relationship: Log belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the letter (polymorphic relationship)
     */
    public function letter()
    {
        if ($this->letter_type === 'incoming') {
            return $this->belongsTo(IncomingLetter::class, 'letter_id');
        }
        return $this->belongsTo(OutgoingLetter::class, 'letter_id');
    }

    /**
     * Scope: Filter by action
     */
    public function scopeByAction($query, $action)
    {
        if ($action) {
            return $query->where('action', $action);
        }
        return $query;
    }

    /**
     * Scope: Filter by letter type
     */
    public function scopeByLetterType($query, $type)
    {
        if ($type) {
            return $query->where('letter_type', $type);
        }
        return $query;
    }

    /**
     * Static method: Create log entry
     */
    public static function createLog($userId, $action, $letterType, $letterId)
    {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'letter_type' => $letterType,
            'letter_id' => $letterId,
        ]);
    }
}
