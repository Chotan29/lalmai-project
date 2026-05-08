<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactDirectoryGroup extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'title','status'];

    public function contact()
    {
        return $this->hasMany(ContactDirectoryDetail::class, 'contact_directory_groups_id','id');
    }
}
