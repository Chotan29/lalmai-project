<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebEvent extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'title', 'slug', 'description', 'event_date', 'event_time', 'event_place','seo_title', 'seo_keywords', 'seo_description', 'image', 'view_count', 'status'];
}
