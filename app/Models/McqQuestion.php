<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class McqQuestion extends BaseModel
{
    protected $fillable = ['created_by', 'last_updated_by', 'subjects_id', 'mcq_question_groups_id', 'mcq_question_levels_id',
        'question', 'explanation', 'image', 'hints', 'mark', 'type', 'status'];

    public function options()
    {
        return $this->hasMany(McqQuestionOption::class, 'mcq_questions_id', 'id');
    }
}