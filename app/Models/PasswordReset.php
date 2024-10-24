<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $primaryKey = "email";
    protected $table = "password_reset_tokens";

    public $timestamps = false;
    protected $fillable = [
        "email",
        "token",
        "created_at",
    ];
}
