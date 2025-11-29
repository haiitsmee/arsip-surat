<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Relationship: Category has many incoming letters
     */
    public function incomingLetters()
    {
        return $this->hasMany(IncomingLetter::class, 'kategori_id');
    }

    /**
     * Relationship: Category has many outgoing letters
     */
    public function outgoingLetters()
    {
        return $this->hasMany(OutgoingLetter::class, 'kategori_id');
    }
}