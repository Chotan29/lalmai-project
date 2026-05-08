<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSetting extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by','form', 'group', 'name','type', 'display_name', 'description','sorting'];
}
