<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebDownload extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'title', 'slug', 'description', 'file', 'status'];
}
