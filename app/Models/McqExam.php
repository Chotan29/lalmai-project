<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class McqExam extends BaseModel
{
    protected $fillable = ['created_by','last_updated_by','title','description','faculty','semester','subjects_id',
        'mcq_instructions_id', 'question_type', 'no_of_question', 'exam_type', 'duration','start_date','end_date',
        'start_date_time','end_date_time','exam_status','mark_type','pass_mark','result_status','status'];


}
