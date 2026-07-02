<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationType extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'title', 'need_approve','status'];
}
