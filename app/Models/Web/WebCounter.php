<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebCounter extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'title','icon', 'counter', 'type', 'rank', 'status'];
}
