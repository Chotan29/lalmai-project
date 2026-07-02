<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebTestimonial extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'name',
        'comment', 'email',  'address', 'office','position','link', 'profile_image', 'status'];
}
