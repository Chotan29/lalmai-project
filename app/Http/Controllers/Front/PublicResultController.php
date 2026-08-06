<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\CollegeBaseController;
use App\Models\ExamMarkLedger;
use App\Models\ExamSchedule;
use App\Models\GeneralSetting;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use ViewHelper;

/**
 * Public result page for lalmaigc.edu.bd - no login at all.
 *
 * Two doors, and they are deliberately very different:
 *
 *   /result              a student types roll + date of birth and sees ONLY their own row.
 *                        Roll numbers are sequential, so the roll alone would let anyone walk
 *                        the whole class; the date of birth is what turns this from a listing
 *                        into a lookup. Wrong roll and wrong date give the same answer, so the
 *                        page cannot be used to confirm who studies here either.
 *
 *   /result/sheet/{token} the whole class on one sheet, department by department, the way the
 *                        college publishes a merit list. The college has chosen to show these
 *                        openly, so the same page lists them.
 *
 * The sheet URL still carries a random token rather than plain ids. Openly listed is not the
 * same as guessable: with a token the office can withdraw a sheet and every copied link dies
 * at once, and nobody can reach a department the college has not published by editing a URL.
 *
 * Both doors re-check tabulation_public_status on every single request, so "remove from
 * website" is immediate - a bookmarked URL stops working straight away.
 *
 * Every figure comes from hscGradingSystem() through the trait, the same engine behind the
 * printed sheet and the student panel, so a result can never differ by which page shows it.
 */
class PublicResultController extends CollegeBaseController
{
    /* ExaminationScope - the grading engine, the tabulation builder and the public-release
       checks - comes in through CollegeBaseController, the same way every other controller
       in this project gets it. */

    protected $base_route = 'public-result';
    protected $view_path = 'front.result';

    /** Wrong tries allowed per IP per hour before the lookup closes. */
    const MAX_ATTEMPTS = 25;

    /**
     * How long a built sheet is reused.
     *
     * Result day is the one day this page gets hammered, and a published sheet is the same
     * for every visitor - so it is built once and served from cache. Without this, every
     * single visitor re-runs the grading engine for the whole class: roughly one query per
     * student plus two per subject per student, which is over four thousand queries for a
     * class of 254. Publishing mints a new token, and the token is part of the cache key, so
     * a fresh publish is served fresh; the window only bounds how late a mark correction
     * shows up.
     */
    const SHEET_CACHE_MINUTES = 10;

    /**
     * Results are personal and should not live in Google's index for ever, nor sit in a
     * shared proxy cache, so every response here says so.
     */
    private function harden($response)
    {
        return $response
            ->header('X-Robots-Tag', 'noindex, nofollow, noarchive')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, private')
            ->header('Referrer-Policy', 'no-referrer');
    }

    /** Turn an exam group row into the label the picker shows. */
    private function groupLabel($row)
    {
        return ViewHelper::getExamById($row->exams_id)
            . ' - ' . ViewHelper::getSemesterTitle($row->semesters_id)
            . ' (' . ViewHelper::getFacultyTitle($row->faculty_id) . ')'
            . ' - ' . ViewHelper::getYearById($row->years_id);
    }

    /** Opaque key for one exam group, so the form never carries five loose ids. */
    private function groupKey($row)
    {
        return $row->years_id . '-' . $row->months_id . '-' . $row->exams_id
            . '-' . $row->faculty_id . '-' . $row->semesters_id;
    }

    /**
     * Resolve a submitted key back to a released exam group.
     * Returns null unless that exact group is currently public, so a crafted key cannot
     * reach an exam the office has not released.
     */
    private function releasedGroupByKey($key)
    {
        foreach ($this->publicReleasedExamGroups() as $row) {
            if (hash_equals($this->groupKey($row), (string) $key)) {
                return $row;
            }
        }

        return null;
    }

    private function examGroupOptions()
    {
        $options = [];
        foreach ($this->publicReleasedExamGroups() as $row) {
            $options[$this->groupKey($row)] = $this->groupLabel($row);
        }

        return $options;
    }

    /**
     * The published sheets, department by department, for the list under the lookup form.
     *
     * Keyed by department name (Science, Humanities, Business ...) because that is how the
     * college announces results and how a visitor looks for their own.
     */
    /**
     * A cheap stamp of what is published right now.
     *
     * One tiny query. Tokens are re-minted on every publish and cleared on withdrawal, so
     * the stamp changes the moment the office changes anything - which is what lets the rest
     * of the page be cached without ever going stale.
     */
    private function publishedStamp()
    {
        $rows = ExamSchedule::where('tabulation_public_status', 1)
            ->distinct()
            ->pluck('tabulation_public_token')
            ->filter()
            ->sort()
            ->implode('|');

        return md5($rows);
    }

    private function releasedSheetsByDepartment()
    {
        $rows = ExamSchedule::select('years_id', 'months_id', 'exams_id', 'faculty_id', 'semesters_id',
                'tabulation_public_token')
            ->where('tabulation_public_status', 1)
            ->whereNotNull('tabulation_public_token')
            ->groupBy('years_id', 'months_id', 'exams_id', 'faculty_id', 'semesters_id',
                'tabulation_public_token')
            ->orderBy('years_id', 'desc')
            ->orderBy('faculty_id', 'asc')
            ->get();

        $departments = [];

        foreach ($rows as $row) {
            $scheduleIds = ExamSchedule::where([
                    ['years_id', '=', $row->years_id],
                    ['months_id', '=', $row->months_id],
                    ['exams_id', '=', $row->exams_id],
                    ['faculty_id', '=', $row->faculty_id],
                    ['semesters_id', '=', $row->semesters_id],
                ])->pluck('id')->all();

            $students = $scheduleIds
                ? ExamMarkLedger::whereIn('exam_schedule_id', $scheduleIds)->distinct()->count('students_id')
                : 0;

            /* A sheet with nobody on it would only confuse a visitor. */
            if ($students < 1) {
                continue;
            }

            $department = ViewHelper::getFacultyTitle($row->faculty_id);

            $departments[$department][] = [
                'title' => ViewHelper::getSemesterTitle($row->semesters_id)
                    . ' - ' . ViewHelper::getExamById($row->exams_id),
                'year' => ViewHelper::getYearById($row->years_id),
                'students' => $students,
                'url' => route('public-result.sheet', ['token' => $row->tabulation_public_token]),
            ];
        }

        return $departments;
    }

    /**
     * The picker options and the department cards, built once per published state.
     *
     * Both are the same for every visitor and change only when the office publishes or
     * withdraws something, which the stamp captures - so this is cached until that happens
     * rather than rebuilt for each of the thousands of visitors on result day.
     */
    private function publicPageData()
    {
        return Cache::remember('pubresult:page:' . $this->publishedStamp(), now()->addHours(6), function () {
            return [
                'exam_groups' => $this->examGroupOptions(),
                'departments' => $this->releasedSheetsByDepartment(),
            ];
        });
    }

    /** The public form, with the published department sheets listed under it. */
    public function index()
    {
        $data = $this->publicPageData();
        $data['generalSetting'] = GeneralSetting::first();

        return $this->harden(response()->view('front.result.index', compact('data')));
    }

    /**
     * Look up one student's result.
     *
     * Every failure path returns the same sentence. Telling the visitor which half was wrong
     * would turn this page into a way to test whether a roll exists, or to fish for a
     * classmate's date of birth.
     */
    public function find(Request $request)
    {
        $notFound = 'No result found for that roll and date of birth. Please check both and try '
            . 'again, or contact the college office.';

        $data = $this->publicPageData();
        $data['generalSetting'] = GeneralSetting::first();
        $data['old_group'] = (string) $request->get('exam_group');
        $data['old_roll'] = trim((string) $request->get('reg_no'));

        $fail = function ($message) use (&$data) {
            $data['error'] = $message;
            return $this->harden(response()->view('front.result.index', compact('data')));
        };

        /* Rate limit before any lookup, so the page cannot be walked roll by roll. */
        $throttleKey = 'pubresult:' . $request->ip();
        $attempts = (int) Cache::get($throttleKey, 0);
        if ($attempts >= self::MAX_ATTEMPTS) {
            return $fail('Too many attempts from this connection. Please try again after an hour, '
                . 'or contact the college office.');
        }

        $roll = trim((string) $request->get('reg_no'));
        $dob = trim((string) $request->get('date_of_birth'));

        if ($roll === '' || $dob === '' || $data['old_group'] === '') {
            return $fail('Please choose the exam and fill in both your roll number and date of birth.');
        }

        $group = $this->releasedGroupByKey($data['old_group']);
        if (!$group) {
            return $fail('That result is not published.');
        }

        /* A wrong try costs an attempt; a correct one does not punish the student. */
        Cache::put($throttleKey, $attempts + 1, now()->addHour());

        $parsedDob = $this->parseDate($dob);
        if (!$parsedDob) {
            return $fail($notFound);
        }

        $student = Student::select('id', 'reg_no', 'first_name', 'middle_name', 'last_name',
                'date_of_birth', 'faculty', 'semester')
            ->whereRaw('TRIM(reg_no) = ?', [$roll])
            ->where('semester', $group->semesters_id)
            ->whereRaw('DATE(date_of_birth) = ?', [$parsedDob])
            ->first();

        if (!$student) {
            return $fail($notFound);
        }

        $result = $this->studentTabulationFor($student->id, $group->years_id, $group->months_id,
            $group->exams_id, $group->faculty_id, $group->semesters_id);

        if (!$result) {
            return $fail('Your result for this exam has not been entered yet. Please contact the college office.');
        }

        /* A correct lookup clears the counter - only wrong guesses accumulate. */
        Cache::forget($throttleKey);

        $data['result'] = $result;
        $data['exam_title'] = $this->groupLabel($group);

        return $this->harden(response()->view('front.result.show', compact('data')));
    }

    /**
     * The whole class sheet for one department.
     *
     * Listed openly on /result, but still addressed by token: compared with hash_equals
     * against the tokens of currently-published groups, so a URL cannot be edited into a
     * department the college has not published, and a withdrawn sheet is dead at once.
     */
    public function sheet($token = null)
    {
        $token = (string) $token;

        if (strlen($token) < 32) {
            abort(404);
        }

        $match = null;
        foreach (ExamSchedule::where('tabulation_public_status', 1)
                    ->whereNotNull('tabulation_public_token')
                    ->select('years_id', 'months_id', 'exams_id', 'faculty_id', 'semesters_id',
                        'tabulation_public_token')
                    ->groupBy('years_id', 'months_id', 'exams_id', 'faculty_id', 'semesters_id',
                        'tabulation_public_token')
                    ->get() as $row) {
            if (hash_equals((string) $row->tabulation_public_token, $token)) {
                $match = $row;
                break;
            }
        }

        if (!$match) {
            abort(404);
        }

        /* Built once, then served from cache. The whole page is identical for every visitor,
           so there is nothing per-person to keep out of it. The token is in the key, so a
           re-publish serves a freshly built sheet immediately. */
        $html = Cache::remember('pubresult:sheet:' . $token,
            now()->addMinutes(self::SHEET_CACHE_MINUTES),
            function () use ($match) {
                return $this->buildSheetHtml($match);
            });

        return $this->harden(response($html));
    }

    /** The expensive part: grade the whole class and render the sheet. */
    private function buildSheetHtml($match)
    {
        $scheduleIds = ExamSchedule::where([
                ['years_id', '=', $match->years_id],
                ['months_id', '=', $match->months_id],
                ['exams_id', '=', $match->exams_id],
                ['faculty_id', '=', $match->faculty_id],
                ['semesters_id', '=', $match->semesters_id],
            ])->pluck('id')->all();

        $studentIds = ExamMarkLedger::whereIn('exam_schedule_id', $scheduleIds)
            ->distinct()->pluck('students_id')->all();

        if (!$studentIds) {
            abort(404);
        }

        $scheduleIdList = implode(',', $scheduleIds);

        $result = $this->hscGradingSystem(new Request([
            'chkIds' => $studentIds,
            'exam_schedule_id' => $scheduleIdList,
            'exams_id' => $match->exams_id,
            'year_id' => $match->years_id,
            'month_id' => $match->months_id,
            'faculty_id' => $match->faculty_id,
            'semester_id' => $match->semesters_id,
        ]));

        if (!is_array($result) || !count($result['student'])) {
            abort(404);
        }

        $data = $this->buildTabulationView($result, $scheduleIdList);
        $data['generalSetting'] = GeneralSetting::first();
        $data['paper'] = 'legal';

        /* Rendered to a string, not returned as a response: it is the HTML that gets cached,
           so repeat visitors skip the grading engine and the blade pass alike. */
        return view('front.result.sheet', compact('data'))->render();
    }

    /**
     * Accept the date the way people actually type it - 2008-02-01 from the date picker,
     * 01/02/2008 or 01-02-2008 typed by hand - without letting strtotime guess at anything
     * looser. Day-first, as everyone here writes it; no month-first guessing, because
     * 05-06-2008 would then silently match two different birthdays.
     */
    private function parseDate($value)
    {
        $value = str_replace(['/', '.'], '-', trim($value));

        foreach (['Y-m-d', 'd-m-Y'] as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date && $date->format($format) === $value) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }
}
