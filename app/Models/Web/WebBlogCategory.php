<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebBlogCategory extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'parent_id', 'title', 'slug', 'description',
        'image', 'status'];

    public function blogs() {
        return $this->hasMany(WebBlog::class, 'category_id');
    }
}
