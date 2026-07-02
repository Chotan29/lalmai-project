<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    protected $fillable = ['name', 'token', 'scopes', 'expires_at'];

    protected $casts = [
        'scopes' => 'array',
        'expires_at' => 'datetime'
    ];
}
