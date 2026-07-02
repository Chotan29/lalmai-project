<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class McqQuestionOption extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'mcq_questions_id', 'option', 'image', 'answer_status', 'status'];
}
