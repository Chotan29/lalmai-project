<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebPage extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'parent_id', 'title', 'slug', 'page_type', 'link', 'image', 'short_desc', 'detail_desc','seo_title', 'seo_keywords', 'seo_description', 'view_count','status'];

    public function menus()
    {
        return $this->belongsToMany(WebMenu::class);
    }

    public function children()
    {
        return $this->hasMany(WebPage::class, 'parent_id', 'id');
    }
}
