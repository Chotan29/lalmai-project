<?php

namespace App\Console\Commands;

use App\Models\ExamMarkLedger;
use App\Models\StudentSubject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Marks saved against a paper the student never took.
 *
 * The mark ledger used to list every student of the class for every subject, so a 0 or an
 * Absent tick could easily land on a student who does not take that paper. Under the HSC
 * rules a single failed compulsory subject drops the whole result to GPA 0.00 / Fail, which
 * is why whole classes were showing as failed.
 *
 * NOTHING IS EVER DELETED. A stray row is set to status = 0 (the mark ledger has a global
 * scope that hides those), written to the log, and recorded in a restore table, so
 * --restore can put every single row back exactly as it was.
 */
class DeactivateUnenrolledMarks extends Command
{
    protected $signature = 'marks:deactivate-unenrolled
                            {--dry-run : List what would change and touch nothing}
                            {--faculty= : Limit to one faculty id}
                            {--semester= : Limit to one semester id}
                            {--with-absent : Also deactivate rows that only carry an Absent tick}
                            {--force-marked : DANGEROUS - also deactivate rows that hold a real mark}
                            {--restore : Put back everything this command deactivated}';

    protected $description = 'Deactivate (never delete) exam marks saved for subjects a student is not enrolled in';

    const RESTORE_TABLE = 'bak_unenrolled_marks';

    public function handle()
    {
        $dry = (bool) $this->option('dry-run');

        $this->ensureRestoreTable();

        if ($this->option('restore')) {
            return $this->restore($dry);
        }

        $all = $this->findStrayRows();

        if ($all->isEmpty()) {
            $this->info('No marks found for unenrolled subjects. Nothing to do.');
            return 0;
        }

        list($empty, $absentOnly, $marked) = $this->bucket($all);

        $this->report($empty, $absentOnly, $marked);

        /*Only completely empty rows are safe to remove: they hold no information at all,
          yet each one fails the student. A row with a real mark is REAL DATA - the student
          clearly sat that paper and their subject list is what is wrong, so it is reported
          and left alone unless someone explicitly forces it.*/
        $rows = $empty;
        if ($this->option('with-absent')) {
            $rows = $rows->merge($absentOnly);
        }
        if ($this->option('force-marked')) {
            $this->warn('--force-marked given: rows holding real marks WILL be deactivated.');
            $rows = $rows->merge($marked);
        }

        if ($rows->isEmpty()) {
            $this->info('Nothing to deactivate with the current options.');
            return 0;
        }

        $this->line('');
        $this->info('Will deactivate '.$rows->count().' row(s).');

        if ($dry) {
            $this->warn('DRY RUN - nothing was changed.');
            return 0;
        }

        $stamp = date('Y-m-d H:i:s');
        $batch = 'unenrolled-'.date('Ymd_His');
        $userId = auth()->id();

        DB::transaction(function () use ($rows, $stamp, $batch, $userId) {
            foreach ($rows as $row) {
                DB::table(self::RESTORE_TABLE)->insert([
                    'batch' => $batch,
                    'ledger_id' => $row->id,
                    'students_id' => $row->students_id,
                    'exam_schedule_id' => $row->exam_schedule_id,
                    'subjects_id' => $row->subjects_id,
                    'previous_status' => $row->status,
                    'obtain_mark_theory' => $row->obtain_mark_theory,
                    'obtain_mark_mcq' => $row->obtain_mark_mcq,
                    'obtain_mark_practical' => $row->obtain_mark_practical,
                    'absent_theory' => $row->absent_theory,
                    'absent_practical' => $row->absent_practical,
                    'created_at' => $stamp,
                ]);

                \Log::warning('Mark deactivated: student not enrolled in this subject', [
                    'batch' => $batch,
                    'ledger_id' => $row->id,
                    'student_id' => $row->students_id,
                    'reg_no' => $row->reg_no,
                    'subject' => $row->subject_title,
                    'schedule_id' => $row->exam_schedule_id,
                    'theory' => $row->obtain_mark_theory,
                    'mcq' => $row->obtain_mark_mcq,
                    'practical' => $row->obtain_mark_practical,
                    'by_user' => $userId,
                ]);

                DB::table('exam_mark_ledgers')->where('id', $row->id)->update([
                    'status' => 0,
                    'last_updated_by' => $userId,
                    'updated_at' => now(),
                ]);
            }
        });

        $this->line('');
        $this->info($rows->count().' mark row(s) deactivated. Batch: '.$batch);
        $this->line('Every row is stored in `'.self::RESTORE_TABLE.'`; run with --restore to undo.');

        return 0;
    }

    /* ------------------------------------------------------------------ finding */

    /**
     * A row is stray when the student HAS a subject list and this subject is not on it.
     * A student with no subject mapping at all is left completely alone - we cannot tell
     * what they take, so their marks must stand.
     */
    private function findStrayRows()
    {
        $query = DB::table('exam_mark_ledgers as l')
            ->join('exam_schedules as e', 'e.id', '=', 'l.exam_schedule_id')
            ->join('subjects as sub', 'sub.id', '=', 'e.subjects_id')
            ->join('students as s', 's.id', '=', 'l.students_id')
            ->where('l.status', 1)
            /* the student has a subject list ... */
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))->from('student_subject as ss')
                  ->whereRaw('ss.students_id = l.students_id');
            })
            /* ... and this subject is not on it */
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))->from('student_subject as ss2')
                  ->whereRaw('ss2.students_id = l.students_id')
                  ->whereRaw('ss2.subjects_id = e.subjects_id');
            })
            ->select('l.id', 'l.students_id', 'l.exam_schedule_id', 'l.status',
                'l.obtain_mark_theory', 'l.obtain_mark_mcq', 'l.obtain_mark_practical',
                'l.absent_theory', 'l.absent_practical',
                'e.subjects_id', 'e.faculty_id', 'e.semesters_id',
                'sub.title as subject_title', 's.reg_no');

        if ($this->option('faculty')) {
            $query->where('e.faculty_id', (int) $this->option('faculty'));
        }

        if ($this->option('semester')) {
            $query->where('e.semesters_id', (int) $this->option('semester'));
        }

        return $query->orderBy('e.faculty_id')->orderBy('sub.title')->orderBy('s.reg_no')->get();
    }

    /**
     * empty      - no mark, no absent tick: pure noise, safe to remove
     * absentOnly - no mark but Absent ticked: probably noise, still a deliberate keystroke
     * marked     - holds a real mark: REAL DATA, never removed by default
     */
    private function bucket($rows)
    {
        $hasMark = function ($r) {
            return (float) $r->obtain_mark_theory > 0
                || (float) $r->obtain_mark_mcq > 0
                || (float) $r->obtain_mark_practical > 0;
        };
        $hasAbsent = function ($r) {
            return (int) $r->absent_theory === 1 || (int) $r->absent_practical === 1;
        };

        $marked = $rows->filter($hasMark)->values();
        $rest = $rows->reject($hasMark);
        $absentOnly = $rest->filter($hasAbsent)->values();
        $empty = $rest->reject($hasAbsent)->values();

        return [$empty, $absentOnly, $marked];
    }

    private function report($empty, $absentOnly, $marked)
    {
        $this->line('');
        $this->info('Marks stored against subjects the student is NOT enrolled in');
        $this->line('  empty rows (no mark, no absent) : '.$empty->count().'   <- safe to deactivate');
        $this->line('  absent-tick only                : '.$absentOnly->count().'   <- use --with-absent to include');
        $this->line('  rows holding a REAL mark        : '.$marked->count().'   <- kept, needs a mapping fix');

        $bySubject = $empty->merge($absentOnly)->groupBy('subject_title');
        if ($bySubject->count()) {
            $this->line('');
            $this->line('  empty / absent-only rows per subject:');
            foreach ($bySubject as $subject => $subjectRows) {
                $this->line(sprintf('    %-46s %4d', $subject, $subjectRows->count()));
            }
        }

        if ($marked->count()) {
            $this->line('');
            $this->warn('  These students HAVE a real mark in a subject missing from their subject list.');
            $this->line('  Nothing will be done to them. Add the subject to the student (Student -> Edit ->');
            $this->line('  Subjects) and the mark counts again automatically:');
            foreach ($marked->groupBy('subject_title') as $subject => $subjectRows) {
                $this->line(sprintf('    %-46s %3d student(s): %s', $subject, $subjectRows->count(),
                    $subjectRows->pluck('reg_no')->take(12)->implode(', ')
                    .($subjectRows->count() > 12 ? ' ...' : '')));
            }
        }
    }

    /* ----------------------------------------------------------------- restoring */

    private function restore($dry)
    {
        $saved = DB::table(self::RESTORE_TABLE)->get();

        if ($saved->isEmpty()) {
            $this->info('Nothing to restore.');
            return 0;
        }

        $this->info('Restoring '.$saved->count().' mark row(s) to their previous status.');

        if ($dry) {
            $this->warn('DRY RUN - nothing was changed.');
            return 0;
        }

        DB::transaction(function () use ($saved) {
            foreach ($saved as $row) {
                DB::table('exam_mark_ledgers')->where('id', $row->ledger_id)->update([
                    'status' => $row->previous_status,
                    'updated_at' => now(),
                ]);
            }
            DB::table(self::RESTORE_TABLE)->truncate();
        });

        $this->info('Restore complete.');
        return 0;
    }

    private function ensureRestoreTable()
    {
        if (DB::getSchemaBuilder()->hasTable(self::RESTORE_TABLE)) {
            return;
        }

        DB::statement('CREATE TABLE `'.self::RESTORE_TABLE.'` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `batch` VARCHAR(64) NULL,
            `ledger_id` INT UNSIGNED NOT NULL,
            `students_id` INT UNSIGNED NULL,
            `exam_schedule_id` INT UNSIGNED NULL,
            `subjects_id` INT UNSIGNED NULL,
            `previous_status` TINYINT NULL,
            `obtain_mark_theory` INT NULL,
            `obtain_mark_mcq` INT NULL,
            `obtain_mark_practical` INT NULL,
            `absent_theory` TINYINT NULL,
            `absent_practical` TINYINT NULL,
            `created_at` TIMESTAMP NULL,
            INDEX (`ledger_id`), INDEX (`batch`)
        ) DEFAULT CHARACTER SET utf8mb4');
    }
}
