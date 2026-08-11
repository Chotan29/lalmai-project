<?php

namespace App\Support;

use App\Http\Controllers\PrintOut\ExamPrintController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ViewHelper;

/**
 * Who passed an exam, and which papers they failed.
 *
 * The transfer screen needs this to promote only the students who passed. It does NOT work the
 * answer out for itself: it asks the same grading engine the tabulation sheet asks, so a student
 * the sheet calls passed is a student the transfer screen calls passed. One rule, one place -
 * two rules would agree today and disagree the day somebody changes a pass mark.
 *
 * The engine marks a failed paper by putting a star in the subject's remark, and a student who
 * has any star gets a Fail at the top. Everything here is read off those stars.
 */
class ExamResultLookup
{
    const ALL = '';
    const PASSED = 'passed';
    const FAILED = 'failed';
    const FAILED_1 = 'failed_1';
    const FAILED_2 = 'failed_2';

    /**
     * The exams that actually have marks entered, newest first.
     *
     * An exam with no marks cannot say who passed, so offering it would only mislead.
     */
    public function groupsWithMarks()
    {
        return DB::table('exam_schedules as es')
            ->join('exam_mark_ledgers as l', 'l.exam_schedule_id', '=', 'es.id')
            ->select('es.years_id', 'es.months_id', 'es.exams_id', 'es.faculty_id', 'es.semesters_id')
            ->groupBy('es.years_id', 'es.months_id', 'es.exams_id', 'es.faculty_id', 'es.semesters_id')
            ->orderByDesc('es.years_id')
            ->orderByDesc('es.months_id')
            ->orderBy('es.faculty_id')
            ->orderBy('es.semesters_id')
            ->get();
    }

    /**
     * Those exams as a dropdown: key -> "Eleventh Final 2026 - Science, Class Eleven".
     */
    public function options()
    {
        $options = ['' => 'Select Exam (optional)'];

        foreach ($this->groupsWithMarks() as $g) {
            $options[$this->key($g)] = sprintf('%s %s - %s, %s',
                ViewHelper::getExamById($g->exams_id),
                ViewHelper::getYearById($g->years_id),
                ViewHelper::getFacultyTitle($g->faculty_id),
                ViewHelper::getSemesterTitle($g->semesters_id));
        }

        return $options;
    }

    public function key($group)
    {
        return implode('-', [(int) $group->years_id, (int) $group->months_id,
            (int) $group->exams_id, (int) $group->faculty_id, (int) $group->semesters_id]);
    }

    /**
     * Turn a posted key back into an exam, but only if it is one we really offer.
     *
     * Checked against the list rather than trusted, so nothing a browser sends can send this
     * off to grade something arbitrary.
     */
    public function parseKey($key)
    {
        $key = trim((string) $key);

        if ($key === '') {
            return null;
        }

        foreach ($this->groupsWithMarks() as $g) {
            if ($this->key($g) === $key) {
                return $g;
            }
        }

        return null;
    }

    /**
     * student id => ['status' => 'Pass'|'Fail', 'failed' => ['Physics -I'], 'failed_count' => 1]
     *
     * Grading a class of 254 is one pass over the marks and about twenty queries, so the screen
     * can afford to do it whenever an exam is chosen.
     */
    public function resultIndex($group)
    {
        $index = [];

        if (!$group) {
            return $index;
        }

        $scheduleIds = DB::table('exam_schedules')
            ->where([
                ['years_id', $group->years_id],
                ['months_id', $group->months_id],
                ['exams_id', $group->exams_id],
                ['faculty_id', $group->faculty_id],
                ['semesters_id', $group->semesters_id],
            ])
            ->orderBy('id')
            ->pluck('id')
            ->all();

        if (!$scheduleIds) {
            return $index;
        }

        $studentIds = DB::table('exam_mark_ledgers')
            ->whereIn('exam_schedule_id', $scheduleIds)
            ->distinct()
            ->pluck('students_id')
            ->all();

        if (!$studentIds) {
            return $index;
        }

        $graded = app(ExamPrintController::class)->hscGradingSystem(new Request([
            'chkIds' => $studentIds,
            'exam_schedule_id' => implode(',', $scheduleIds),
            'exams_id' => $group->exams_id,
            'year_id' => $group->years_id,
            'month_id' => $group->months_id,
            'faculty_id' => $group->faculty_id,
            'semester_id' => $group->semesters_id,
        ]));

        if (!is_array($graded) || !isset($graded['student'])) {
            return $index;
        }

        foreach ($graded['student'] as $student) {
            $failed = [];
            $failedOptional = [];

            foreach ($student->subjects as $subject) {
                if (trim((string) ($subject->subject_result ?? '')) !== 'Fail') {
                    continue;
                }

                $title = trim((string) ($subject->title ?? $subject->code ?? ''));

                /*A failed 4th subject does not fail a student - it only stops counting
                  towards the GPA. Keeping the two lists apart matters: the compulsory list
                  is what a carry-subject rule would work on, the optional one is a note.*/
                if (!empty($subject->is_optional)) {
                    $failedOptional[] = $title;
                } else {
                    $failed[] = $title;
                }
            }

            /*Pass or fail is the engine's own verdict, not a second opinion worked out here.
              The tabulation sheet reads this very field, so the two can never drift apart.*/
            $passed = trim((string) ($student->remark ?? '')) === 'Pass';

            $index[(int) $student->id] = [
                'status' => $passed ? 'Pass' : 'Fail',
                'failed' => $failed,
                'failed_count' => count($failed),
                'failed_optional' => $failedOptional,
            ];
        }

        return $index;
    }

    /**
     * The Pass/Fail dropdown.
     *
     * "Failed 1 subject" is here because it is the real question the office asks. At Eleventh
     * Final, 98 of 254 Science students passed outright; of the rest, a large group failed
     * exactly one compulsory paper - and those are the students a carry-subject rule would let
     * up a year. The count is of compulsory papers only, because a failed 4th subject never
     * held anybody back.
     */
    public function filterOptions()
    {
        return [
            self::ALL => 'All Students',
            self::PASSED => 'Passed (all subjects)',
            self::FAILED => 'Failed (any subject)',
            self::FAILED_1 => 'Failed 1 subject only',
            self::FAILED_2 => 'Failed 2 subjects only',
        ];
    }

    /**
     * Does one student's result match what the office asked for?
     *
     * A student with no row in the index did not sit this exam. They are never counted as
     * passed - promoting somebody on a result that does not exist is exactly the mistake this
     * screen is meant to prevent.
     */
    public function matches($result, $filter)
    {
        $filter = trim((string) $filter);

        if ($filter === self::ALL) {
            return true;
        }

        if (!$result) {
            return false;
        }

        switch ($filter) {
            case self::PASSED:
                return $result['status'] === 'Pass';
            case self::FAILED:
                return $result['status'] === 'Fail';
            case self::FAILED_1:
                return $result['failed_count'] === 1;
            case self::FAILED_2:
                return $result['failed_count'] === 2;
        }

        return true;
    }

    /**
     * A short count of how the filtered list breaks down, for the line above the table.
     */
    public function summarise($students)
    {
        $summary = ['listed' => 0, 'passed' => 0, 'failed' => 0, 'no_result' => 0];

        foreach ($students as $student) {
            $summary['listed']++;
            $result = isset($student->exam_result) ? $student->exam_result : null;

            if (!$result) {
                $summary['no_result']++;
            } elseif ($result['status'] === 'Pass') {
                $summary['passed']++;
            } else {
                $summary['failed']++;
            }
        }

        return $summary;
    }
}
