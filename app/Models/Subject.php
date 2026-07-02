<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Subject extends Model
{
    protected $fillable = ['created_by', 'last_updated_by', 'title', 'code','course_fee', 'full_mark_theory', 'pass_mark_theory',
        'full_mark_practical', 'pass_mark_practical', 'mcq_number_theory', 'mcq_number_practical', 'credit_hour', 'sub_type', 'class_type', 'staff_id',
        'description', 'status'];

    public function semester()
    {
        return $this->belongsToMany(Semester::class);
    }

    /*Multiple teachers can take one subject*/
    public function teachers()
    {
        return $this->belongsToMany(Staff::class, 'subject_staff', 'subject_id', 'staff_id');
    }

    /*Filter subjects a teacher is allowed to manage (pivot + legacy staff_id column)*/
    public function scopeForTeacher($query, $staffId)
    {
        return $query->where(function ($q) use ($staffId) {
            $q->where('subjects.staff_id', $staffId)
              ->orWhereIn('subjects.id', function ($sub) use ($staffId) {
                  $sub->select('subject_id')
                      ->from('subject_staff')
                      ->where('staff_id', $staffId);
              });
        });
    }

    // Add model event for deletion logging
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($subject) {
            $subject->teachers()->detach();

            DB::table('deletion_logs')->insert([
                'model' => 'Subject',
                'model_id' => $subject->id,
                'data' => json_encode($subject->attributesToArray()),
                'user_id' => auth()->id() ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
