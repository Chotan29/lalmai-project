<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebSlider extends BaseModel
{
    protected $fillable = ['created_at', 'updated_at', 'created_by', 'last_updated_by', 'title',
        'description', 'image_name', 'link', 'button_text', 'open_in', 'rank', 'status'];
}
