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

    // Add model event for deletion logging
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($subject) {
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
