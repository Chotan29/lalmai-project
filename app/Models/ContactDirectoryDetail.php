<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactDirectoryDetail extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'contact_directory_groups_id','name','sex','date_of_birth', 'religion',
        'caste','nationality', 'mother_tongue','blood_group', 'address','tel','cell_1','cell_2','email', 'mailing_address',
        'mailing_tel','mailing_cell_1','mailing_cell_2','mailing_email', 'image','status'];
}
