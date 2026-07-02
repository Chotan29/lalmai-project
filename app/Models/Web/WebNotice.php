<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebNotice extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'title', 'slug', 'description', 'publish_date',
        'seo_title', 'seo_keywords', 'seo_description', 'image','file', 'view_count', 'status'];
}
