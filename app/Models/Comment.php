<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'article_id',
        'komentar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function artikel()
    {
        return $this->belongsTo(Article::class);
    }
}
