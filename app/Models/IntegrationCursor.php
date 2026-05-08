<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationCursor extends Model
{
    protected $table = 'integration_cursors';
    protected $fillable = ['key','value'];
}
