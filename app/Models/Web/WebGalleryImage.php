<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebGalleryImage extends BaseModel
{
    protected $fillable = ['created_at', 'updated_at', 'created_by', 'last_updated_by', 'gallery_id', 'caption',
        'alt_text', 'image', 'rank', 'status'];
}
