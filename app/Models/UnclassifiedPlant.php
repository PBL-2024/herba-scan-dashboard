<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnclassifiedPlant extends Model
{
    protected $fillable = [
        'user_id',
        'nama',
        'file',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
