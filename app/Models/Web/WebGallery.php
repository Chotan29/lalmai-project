<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebGallery extends BaseModel
{
    protected $fillable = ['created_at', 'updated_at', 'created_by', 'last_updated_by', 'title', 'slug',
        'description', 'image_name', 'rank', 'view_count', 'status'];

    public function images()
    {
        return $this->hasMany(WebGalleryImage::class, 'gallery_id');
    }
}
