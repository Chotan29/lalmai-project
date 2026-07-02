<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebService extends BaseModel
{
    protected $fillable = ['created_at', 'updated_at', 'created_by', 'last_updated_by', 'title','sub_title',
        'description', 'image','video', 'link', 'button_text', 'open_in', 'rank', 'status'];
}
