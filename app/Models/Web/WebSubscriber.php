<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebSubscriber extends BaseModel
{
    protected $table = 'web_subscribers';
    protected $fillable = ['name','email', 'status'];
}
