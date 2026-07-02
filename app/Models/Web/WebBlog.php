<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebBlog extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'category_id', 'title', 'slug', 'short_desc', 'detail_desc', 'publish_date', 'seo_title', 'seo_keywords', 'seo_description', 'image', 'view_count', 'status'];

    public function category()
    {
        return $this->belongsTo(WebBlogCategory::class, 'category_id');
    }
}
