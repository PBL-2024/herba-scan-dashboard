<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Plant extends Model
{
    protected $fillable = [
        'user_id',
        'cover',
        'nama',
        'deskripsi',
        'manfaat',
        'pengolahan',
        'total_view',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
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
