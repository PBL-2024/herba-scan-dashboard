<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class UnclassifiedPlant extends Model
{
    protected $fillable = [
        'user_id',
        'nama',
        'file',
        'is_verified',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $appends = [
        'file_url',
    ];

    public function getFileUrlAttribute()
    {
        return Storage::disk('public')->url($this->file);
    }
}
