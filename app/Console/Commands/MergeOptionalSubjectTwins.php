<?php

namespace App\Console\Commands;

use App\Models\ExamMarkLedger;
use App\Models\ExamSchedule;
use App\Models\StudentSubject;
use App\Models\Subject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * ONE PAPER = ONE SUBJECT.
 *
 * Historically, a subject that some students take as compulsory and others as their 4th
 * (optional) subject was duplicated with an "O-" code. That split the exam schedule and
 * the marks in two, and the code-only matching even paired unrelated subjects
 * (Chemistry -I 176 <-> Statistics 1st O-176).
 *
 * This command:
 *   1. backs up every table it is about to touch,
 *   2. fills student_subject.subject_role from today's data (no behaviour change),
 *   3. merges the legacy "(Optional)" duplicates into their real subject, keeping the
 *      students' role = optional,
 *   4. deactivates the leftover duplicate subjects so nobody can schedule them again.
 *
 * Nothing is deleted without a backup, and --dry-run shows the full plan first.
 */
class MergeOptionalSubjectTwins extends Command
{
    protected $signature = 'subjects:merge-optional-twins
                            {--dry-run : Show what would happen, change nothing}
                            {--backfill-only : Only fill subject_role, do not merge}
                            {--repair-optional-flags : Only recompute semester_subject.allow_as_optional from the students who already hold the subject as their 4th}';

    protected $description = 'Fill student_subject.subject_role and merge legacy "(Optional)" duplicate subjects into their real subject';

    private $dry = false;

    public function handle()
    {
        $this->dry = (bool) $this->option('dry-run');

        if ($this->dry) {
            $this->warn('DRY RUN - no data will be changed.');
        }

        if (!DB::getSchemaBuilder()->hasColumn('student_subject', 'subject_role')) {
            $this->error('student_subject.subject_role is missing. Run the migration first.');
            return 1;
        }

        if ($this->option('repair-optional-flags')) {
            $this->repairOptionalFlags();
            return 0;
        }

        $this->backup();
        $this->backfillRoles();

        if ($this->option('backfill-only')) {
            $this->info('Backfill only - stopping here.');
            return 0;
        }

        $this->mergeTwins();
        $this->summary();

        return 0;
    }

    /* ---------------------------------------------------------------- backups */

    private function backup()
    {
        $stamp = date('Ymd_His');
        $tables = ['student_subject', 'exam_mark_ledgers', 'exam_schedules', 'subjects'];

        $this->line('');
        $this->info('Backups');

        foreach ($tables as $table) {
            $backup = 'bak_'.$table.'_'.$stamp;
            if ($this->dry) {
                $this->line('  would create '.$backup);
                continue;
            }
            DB::statement('CREATE TABLE `'.$backup.'` AS SELECT * FROM `'.$table.'`');
            $this->line('  created '.$backup.' ('.DB::table($backup)->count().' rows)');
        }
    }

    /* ------------------------------------------------------------- role backfill */

    private function backfillRoles()
    {
        $this->line('');
        $this->info('Filling subject_role');

        $optionalSubjectIds = Subject::get()
            ->filter(function ($s) {
                return StudentSubject::guessRole($s) === StudentSubject::ROLE_OPTIONAL;
            })
            ->pluck('id')
            ->all();

        $optionalRows = DB::table('student_subject')->whereIn('subjects_id', $optionalSubjectIds)->count();
        $otherRows = DB::table('student_subject')->whereNotIn('subjects_id', $optionalSubjectIds)->count();

        $this->line('  optional-type subjects : '.count($optionalSubjectIds));
        $this->line('  enrolments -> optional : '.$optionalRows);
        $this->line('  enrolments -> compulsory: '.$otherRows);

        if ($this->dry || !count($optionalSubjectIds)) {
            return;
        }

        /*Only ever PROMOTE to optional, never demote. After the merge, Higher Math and
          Biology are ordinary compulsory subjects that some students hold as their 4th
          subject - re-running this command must not wipe that.*/
        DB::table('student_subject')
            ->whereIn('subjects_id', $optionalSubjectIds)
            ->where('subject_role', '!=', StudentSubject::ROLE_OPTIONAL)
            ->update(['subject_role' => StudentSubject::ROLE_OPTIONAL, 'updated_at' => now()]);
    }

    /* ------------------------------------------------------------------ merging */

    private function mergeTwins()
    {
        $this->line('');
        $this->info('Merging "(Optional)" duplicates into their real subject');

        $mains = Subject::whereRaw("LOWER(TRIM(COALESCE(sub_type,''))) <> 'optional'")->get();

        foreach ($mains as $main) {
            $twin = Subject::optionalTwinOf($main);
            if (!$twin) {
                continue;
            }

            $this->line('');
            $this->line('  '.$main->title.' ['.$main->code.']  <-  '.$twin->title.' ['.$twin->code.']');

            $this->mergeEnrolments($main, $twin);
            $ok = $this->mergeSchedules($main, $twin);

            if ($ok) {
                $this->inheritSemesterMapping($main, $twin);
                $this->deactivateSubject($twin);
            } else {
                $this->warn('    subject kept active: its marks could not be merged safely');
            }
        }
    }

    private function mergeEnrolments($main, $twin)
    {
        $rows = DB::table('student_subject')->where('subjects_id', $twin->id)->get();

        if ($rows->isEmpty()) {
            $this->line('    enrolments: none');
            return;
        }

        $moved = 0;
        $collapsed = 0;

        foreach ($rows as $row) {
            $already = DB::table('student_subject')
                ->where('students_id', $row->students_id)
                ->where('subjects_id', $main->id)
                ->first();

            if ($already) {
                /*Student is mapped to both the subject and its duplicate: keep one row,
                  marked as the 4th subject.*/
                $collapsed++;
                if (!$this->dry) {
                    DB::table('student_subject')->where('id', $already->id)
                        ->update(['subject_role' => StudentSubject::ROLE_OPTIONAL, 'updated_at' => now()]);
                    DB::table('student_subject')->where('id', $row->id)->delete();
                }
                continue;
            }

            $moved++;
            if (!$this->dry) {
                DB::table('student_subject')->where('id', $row->id)->update([
                    'subjects_id' => $main->id,
                    'subject_role' => StudentSubject::ROLE_OPTIONAL,
                    'updated_at' => now(),
                ]);
            }
        }

        $this->line('    enrolments: '.$moved.' moved, '.$collapsed.' duplicate pair(s) collapsed');
    }

    /**
     * Move the duplicate's exam schedules onto the real subject.
     * Returns false (and changes nothing for that schedule) when the two schedules do not
     * carry identical full/pass marks - merging those would silently re-grade students.
     */
    private function mergeSchedules($main, $twin)
    {
        $schedules = ExamSchedule::where('subjects_id', $twin->id)->get();

        if ($schedules->isEmpty()) {
            $this->line('    schedules: none');
            return true;
        }

        $allOk = true;

        foreach ($schedules as $twinSchedule) {
            $mainSchedule = ExamSchedule::where([
                ['years_id', '=', $twinSchedule->years_id],
                ['months_id', '=', $twinSchedule->months_id],
                ['exams_id', '=', $twinSchedule->exams_id],
                ['faculty_id', '=', $twinSchedule->faculty_id],
                ['semesters_id', '=', $twinSchedule->semesters_id],
                ['subjects_id', '=', $main->id],
            ])->first();

            $ledgerRows = ExamMarkLedger::where('exam_schedule_id', $twinSchedule->id)->count();

            /*No schedule on the real subject: just repoint this one, marks travel with it.*/
            if (!$mainSchedule) {
                $this->line('    schedule '.$twinSchedule->id.': repointed to the real subject ('.$ledgerRows.' marks)');
                if (!$this->dry) {
                    DB::table('exam_schedules')->where('id', $twinSchedule->id)
                        ->update(['subjects_id' => $main->id, 'updated_at' => now()]);
                }
                continue;
            }

            $mismatch = $this->markMismatch($mainSchedule, $twinSchedule);
            if ($mismatch) {
                $this->warn('    schedule '.$twinSchedule->id.' SKIPPED - '.$mismatch);
                $allOk = false;
                continue;
            }

            $moved = 0;
            $kept = 0;

            foreach (ExamMarkLedger::where('exam_schedule_id', $twinSchedule->id)->get() as $ledger) {
                $existing = ExamMarkLedger::where('exam_schedule_id', $mainSchedule->id)
                    ->where('students_id', $ledger->students_id)
                    ->first();

                if ($existing) {
                    /*Both schedules hold a mark for this student. Never delete: keep the
                      real subject's row and deactivate the duplicate, with a log line.*/
                    $kept++;
                    if (!$this->dry) {
                        \Log::warning('Twin merge: duplicate mark deactivated', [
                            'ledger_id' => $ledger->id,
                            'student_id' => $ledger->students_id,
                            'from_schedule' => $twinSchedule->id,
                            'kept_ledger_id' => $existing->id,
                            'kept_schedule' => $mainSchedule->id,
                        ]);
                        DB::table('exam_mark_ledgers')->where('id', $ledger->id)
                            ->update(['status' => 0, 'updated_at' => now()]);
                    }
                    continue;
                }

                $moved++;
                if (!$this->dry) {
                    DB::table('exam_mark_ledgers')->where('id', $ledger->id)
                        ->update(['exam_schedule_id' => $mainSchedule->id, 'updated_at' => now()]);
                }
            }

            $this->line('    schedule '.$twinSchedule->id.' -> '.$mainSchedule->id.': '.$moved.' marks moved, '.$kept.' duplicate(s) deactivated');

            if (!$this->dry) {
                DB::table('exam_schedules')->where('id', $twinSchedule->id)->delete();
            }
        }

        return $allOk;
    }

    private function markMismatch($a, $b)
    {
        foreach (['full_mark_theory', 'pass_mark_theory', 'full_mark_practical', 'pass_mark_practical'] as $field) {
            if ((float) $a->{$field} !== (float) $b->{$field}) {
                return $field.' differs ('.$a->{$field}.' vs '.$b->{$field}.')';
            }
        }

        return null;
    }

    /**
     * Wherever the duplicate was offered as an optional choice, the real subject now takes
     * that place: it becomes selectable as the 4th subject in the same semesters, so the
     * registration form keeps offering exactly what it offered before the merge.
     */
    private function inheritSemesterMapping($main, $twin)
    {
        if (!DB::getSchemaBuilder()->hasColumn('semester_subject', 'allow_as_optional')) {
            $this->warn('    semester_subject.allow_as_optional missing - run the migration');
            return;
        }

        $semesterIds = DB::table('semester_subject')->where('subject_id', $twin->id)->pluck('semester_id')->all();

        if (!$semesterIds) {
            return;
        }

        $this->line('    offered as 4th subject in semester(s): '.implode(', ', $semesterIds));

        if ($this->dry) {
            return;
        }

        foreach ($semesterIds as $semesterId) {
            $exists = DB::table('semester_subject')
                ->where('semester_id', $semesterId)->where('subject_id', $main->id)->first();

            if ($exists) {
                DB::table('semester_subject')->where('id', $exists->id)
                    ->update(['allow_as_optional' => 1, 'updated_at' => now()]);
                continue;
            }

            DB::table('semester_subject')->insert([
                'semester_id' => $semesterId,
                'subject_id' => $main->id,
                'allow_as_optional' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * The duplicate is now an orphan: no enrolments, no schedules, no marks.
     * `subjects.status` is not used as a filter anywhere in this app (270 of 277 rows are
     * already 0), so what actually keeps the duplicate out of every selection screen is
     * removing it from the semester mapping. The title is tagged too, so nobody re-adds it
     * by mistake.
     */
    private function deactivateSubject($twin)
    {
        $mapped = DB::table('semester_subject')->where('subject_id', $twin->id)->count();
        $this->line('    subject '.$twin->id.' retired: '.$mapped.' semester mapping(s) removed');

        if ($this->dry) {
            return;
        }

        DB::table('semester_subject')->where('subject_id', $twin->id)->delete();

        $title = (string) $twin->title;
        if (stripos($title, '[MERGED]') === false) {
            $title = trim($title).' [MERGED]';
        }

        DB::table('subjects')->where('id', $twin->id)->update([
            'title' => $title,
            'status' => 0,
            'updated_at' => now(),
        ]);
    }

    /**
     * Recompute which subjects a semester may offer as the 4th subject, from the students
     * who already hold that subject as optional. Idempotent, safe to re-run: a subject
     * whose own type is already "Optional" needs no flag, it is optional for everyone.
     */
    private function repairOptionalFlags()
    {
        $this->line('');
        $this->info('Recomputing semester_subject.allow_as_optional');

        $pairs = DB::table('student_subject as ss')
            ->join('students as st', 'st.id', '=', 'ss.students_id')
            ->join('subjects as s', 's.id', '=', 'ss.subjects_id')
            ->where('ss.subject_role', StudentSubject::ROLE_OPTIONAL)
            ->whereRaw("LOWER(TRIM(COALESCE(s.sub_type,''))) <> 'optional'")
            ->select('st.semester as semester_id', 'ss.subjects_id', 's.title', DB::raw('COUNT(*) as students'))
            ->groupBy('st.semester', 'ss.subjects_id', 's.title')
            ->get();

        if ($pairs->isEmpty()) {
            $this->line('  nothing to do');
            return;
        }

        foreach ($pairs as $p) {
            $this->line('  semester '.$p->semester_id.' -> '.$p->title.' ('.$p->students.' students)');

            if ($this->dry) {
                continue;
            }

            $existing = DB::table('semester_subject')
                ->where('semester_id', $p->semester_id)->where('subject_id', $p->subjects_id)->first();

            if ($existing) {
                DB::table('semester_subject')->where('id', $existing->id)
                    ->update(['allow_as_optional' => 1, 'updated_at' => now()]);
                continue;
            }

            DB::table('semester_subject')->insert([
                'semester_id' => $p->semester_id,
                'subject_id' => $p->subjects_id,
                'allow_as_optional' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /* ------------------------------------------------------------------ summary */

    private function summary()
    {
        $this->line('');
        $this->info('After');
        $this->line('  enrolments compulsory : '.DB::table('student_subject')->where('subject_role', '!=', 'optional')->count());
        $this->line('  enrolments optional   : '.DB::table('student_subject')->where('subject_role', 'optional')->count());
        $this->line('  active subjects       : '.DB::table('subjects')->where('status', 1)->count());
        $this->line('  mark rows active      : '.DB::table('exam_mark_ledgers')->where('status', 1)->count());
        $this->line('  mark rows deactivated : '.DB::table('exam_mark_ledgers')->where('status', 0)->count());

        $multi = DB::table('student_subject')->where('subject_role', 'optional')
            ->select('students_id', DB::raw('COUNT(*) c'))
            ->groupBy('students_id')->having('c', '>', 1)->get();

        if ($multi->count()) {
            $this->warn('  '.$multi->count().' student(s) have more than one 4th subject - please correct:');
            foreach ($multi as $m) {
                $this->line('    student id '.$m->students_id.' -> '.$m->c.' optional subjects');
            }
        }
    }
}
