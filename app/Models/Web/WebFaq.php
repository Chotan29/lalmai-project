<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebFaq extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'question', 'answer', 'rank', 'status'];
}
