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

    /*Mark fields that must stay in sync with exam_schedules whenever they change on the subject*/
    protected static $syncedMarkFields = ['full_mark_theory', 'pass_mark_theory', 'full_mark_practical', 'pass_mark_practical'];

    /**
     * Split a subject title into [name, paperNumber].
     *  "Chemistry -I"                  -> ['chemistry', '1']
     *  "Biology - II (Optional)"       -> ['biology', '2']
     *  "Economics 1st Paper(Optional)" -> ['economics', '1']
     *  "Higher Mathematics -I"         -> ['highermathematics', '1']
     */
    public static function titleParts($title)
    {
        $t = ' '.strtolower((string) $title).' ';
        $t = str_replace(['(', ')', '[', ']', '.', ',', '&', '-', '_', '/', ':'], ' ', $t);
        $t = preg_replace('/\b(optional|compulsory|paper|part|course|subject)\b/', ' ', $t);

        $ordinals = [
            '1' => '1', '1st' => '1', 'first' => '1', 'i' => '1',
            '2' => '2', '2nd' => '2', 'second' => '2', 'ii' => '2',
            '3' => '3', '3rd' => '3', 'third' => '3', 'iii' => '3',
            '4' => '4', '4th' => '4', 'fourth' => '4', 'iv' => '4',
        ];

        $paper = '';
        $name = '';
        foreach (preg_split('/\s+/', trim($t), -1, PREG_SPLIT_NO_EMPTY) as $token) {
            if (isset($ordinals[$token])) {
                $paper = $ordinals[$token];
                continue;
            }
            $name .= preg_replace('/[^a-z0-9]/', '', $token);
        }

        return [$name, $paper];
    }

    /**
     * True when two titles describe the same paper of the same subject.
     * Same paper number is mandatory; names must be identical or one a prefix of the other
     * ("Higher Math" vs "Higher Mathematics"). Guards against the code convention alone
     * pairing unrelated subjects, e.g. "Chemistry -I" (176) and "Statistics 1st" (O-176).
     */
    public static function sameName($titleA, $titleB)
    {
        list($nameA, $paperA) = static::titleParts($titleA);
        list($nameB, $paperB) = static::titleParts($titleB);

        if ($nameA === '' || $nameB === '' || $paperA !== $paperB) {
            return false;
        }
        if ($nameA === $nameB) {
            return true;
        }

        $shorter = strlen($nameA) <= strlen($nameB) ? $nameA : $nameB;
        $longer = strlen($nameA) <= strlen($nameB) ? $nameB : $nameA;

        return strlen($shorter) >= 4 && strpos($longer, $shorter) === 0;
    }

    /**
     * The legacy "(Optional)" duplicate of this subject, matched by the O-/OP- code
     * convention AND by name. Returns null when nothing trustworthy matches.
     */
    public static function optionalTwinOf($subject)
    {
        if (!is_object($subject) || strtolower(trim((string) $subject->sub_type)) === 'optional') {
            return null;
        }

        $norm = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $subject->code));
        if ($norm === '') {
            return null;
        }

        return static::whereRaw("UPPER(REPLACE(REPLACE(REPLACE(code, '-', ''), ' ', ''), '_', '')) IN (?, ?)", ['O'.$norm, 'OP'.$norm])
            ->where('id', '!=', $subject->id)
            ->get()
            ->first(function ($s) use ($subject) {
                $isOptional = strtolower(trim((string) $s->sub_type)) === 'optional'
                    || stripos((string) $s->title, 'optional') !== false;

                return $isOptional && static::sameName($subject->title, $s->title);
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

        /*Whenever a subject's marks are edited, push the same marks into every
          exam schedule row already created for that subject (including published ones),
          so subject-master marks and exam-schedule marks never drift apart.*/
        static::updated(function ($subject) {
            $changedMarkFields = array_intersect(static::$syncedMarkFields, array_keys($subject->getChanges()));

            if (empty($changedMarkFields)) {
                return;
            }

            DB::table('exam_schedules')
                ->where('subjects_id', $subject->id)
                ->update([
                    'full_mark_theory' => $subject->full_mark_theory,
                    'pass_mark_theory' => $subject->pass_mark_theory,
                    'full_mark_practical' => $subject->full_mark_practical,
                    'pass_mark_practical' => $subject->pass_mark_practical,
                    'updated_at' => now(),
                ]);
        });
    }
}
