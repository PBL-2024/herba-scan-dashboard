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
        'short_desc',
        'cover',
        'total_view',
        'tanggal_publikasi',
    ];

    protected $appends = ['cover_url'];

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
    public function getCoverUrlAttribute($value)
    {
        // Check if the value is a path and not a full URL
        if ($this->cover && !filter_var($this->cover, FILTER_VALIDATE_URL)) {
            return Storage::disk('public')->url($this->cover);
        }
        return $this->cover;
    }
}
