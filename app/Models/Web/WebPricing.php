<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebPricing extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'title','old_price','new_price', 'per_term', 'description', 'image','tag','tag_color','rank', 'link', 'button_text', 'open_in','status'];
}
