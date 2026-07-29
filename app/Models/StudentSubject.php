<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSubject extends BaseModel
{
    const ROLE_COMPULSORY = 'compulsory';
    const ROLE_OPTIONAL = 'optional';

    protected $table = 'student_subject';
    protected $fillable = ['created_by', 'last_updated_by', 'students_id',  'subjects_id', 'subject_role', 'status'];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subjects_id', 'id');
    }

    public function scopeOptional($query)
    {
        return $query->where('subject_role', self::ROLE_OPTIONAL);
    }

    public function scopeCompulsory($query)
    {
        return $query->where('subject_role', '!=', self::ROLE_OPTIONAL);
    }

    /**
     * The role a subject should get for a student when nothing is stored yet.
     * Falls back to the old convention: a subject whose own type says "Optional",
     * or a legacy O-/OP- coded twin, is the student's 4th subject.
     */
    public static function guessRole($subject)
    {
        if (!is_object($subject)) {
            return self::ROLE_COMPULSORY;
        }

        if (strtolower(trim((string) $subject->sub_type)) === 'optional') {
            return self::ROLE_OPTIONAL;
        }

        if (preg_match('/^OP?[-_ ]/', strtoupper(trim((string) $subject->code)))) {
            return self::ROLE_OPTIONAL;
        }

        if (stripos((string) $subject->title, 'optional') !== false) {
            return self::ROLE_OPTIONAL;
        }

        return self::ROLE_COMPULSORY;
    }
}
