<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebContactList extends BaseModel
{
    protected $fillable = ['name', 'email', 'address', 'phone', 'message', 'view_status'];
}
