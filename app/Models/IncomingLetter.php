<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingLetter extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'no_surat',
        'pengirim',
        'tanggal_surat',
        'perihal',
        'kategori_id',
        'file_path',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_surat' => 'date',
    ];

    /**
     * Relationship: Incoming letter belongs to a category
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }

    /**
     * Relationship: Incoming letter belongs to a user (creator)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Incoming letter has many logs
     */
    public function logs()
    {
        return $this->morphMany(Log::class, 'letter');
    }

    /**
     * Scope: Search letters
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('no_surat', 'like', "%{$search}%")
              ->orWhere('perihal', 'like', "%{$search}%")
              ->orWhere('pengirim', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Filter by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        if ($categoryId) {
            return $query->where('kategori_id', $categoryId);
        }
        return $query;
    }
}