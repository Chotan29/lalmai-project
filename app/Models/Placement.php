<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Placement extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'title','address','phone','email','description','status'];
}
