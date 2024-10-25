<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
