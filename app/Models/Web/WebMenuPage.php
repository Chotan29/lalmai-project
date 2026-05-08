<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class WebMenuPage extends Model
{
    protected $table = 'web_menu_web_page';
    protected $fillable = ['created_by', 'last_updated_by', 'menu_id', 'page_id', 'rank'];
}
