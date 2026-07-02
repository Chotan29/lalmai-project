<?php

namespace App\Models\Web;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class WebMenu extends BaseModel
{
    protected $fillable = ['title', 'slug', 'rank', 'status'];

    public function pages()
    {
        return $this->belongsToMany(WebPage::class);
    }
}
