<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Article extends Model
{
    protected $fillable = [
        'user_id',
        'judul',
        'isi',
        'cover',
        'total_view',
        'tanggal_publikasi',
    ];

    public function getCoverUrlAttribute()
    {
        return asset(path: 'storage/' . $this->cover);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the cover attribute.
     * @param mixed $value
     * @return mixed
     */
    public function getCoverAttribute($value)
    {
        // Check if the value is a path and not a full URL
        if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
            return Storage::disk('public')->url($value);
        }
        return $value;
    }
}
