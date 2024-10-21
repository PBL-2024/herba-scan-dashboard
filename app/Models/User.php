<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'email_verified_at',
        'image_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function komentar()
    {
        return $this->hasMany(Comment::class);
    }

    public function artikel()
    {
        return $this->hasMany(Article::class);
    }

    public function avatar()
    {
        // check if not url return with assets
        if ($this->image_urk != null) {
            if (filter_var($this->image_url, FILTER_VALIDATE_URL) === false) {
                return asset('storage/' . $this->image_url);
            }
            return $this->image_url;
        }
        return null;
    }

    public function unclassifiedPlant()
    {
        return $this->hasMany(UnclassifiedPlant::class);
    }
}
