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

    protected $hidden = [
        'user_id',
        'article_id',
    ];

    protected $appends = [
        'my_comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function getMyCommentAttribute()
    {
        return auth()->check() && auth()->id() == $this->user_id;
    }
}
