<?php
namespace App\Traits;

use App\Models\Annexure;
use App\Models\AttendanceStatus;
use App\Models\Degree;
use App\Models\GradingType;
use App\Models\Placement;
use App\Models\Scholarship;
use App\Models\State;
use App\Models\StudentStatus;
use App\Models\Subject;

trait AcademicScope{

    public function getGradingTitle($id)
    {
        $grading = GradingType::find($id);
        if ($grading) {
            return $grading->title;
        }else{
            return "";
        }
    }

    /**
     * The same list again, reached through a different trait. Held for the request, for the
     * same reason: a list screen asks it once per row for a table with a handful of rows.
     * Its own store, because a class may use this trait without StudentScopes.
     */
    protected static $academicStatusCache = null;

    public function getAcademicStatus($id)
    {
        if (self::$academicStatusCache === null) {
            self::$academicStatusCache = StudentStatus::pluck('title', 'id')->all();
        }

        return self::$academicStatusCache[$id] ?? "Unknown";
    }

    public function getAttendanceFullStatus($id)
    {
        $status = AttendanceStatus::find($id);
        if ($status) {
            return strtoupper($status->title);
        }else{
            return "-";
        }
    }

    public function getAttendanceStatus($id)
    {
        $status = AttendanceStatus::find($id);
        if ($status) {
            return strtoupper(substr($status->title,'0','2'));
        }else{
            return "-";
        }
    }

    public function getAttendanceStatusFullText($id)
    {
        $status = AttendanceStatus::find($id);
        if ($status) {
            return strtoupper($status->title);
        }else{
            return "-";
        }
    }

    public function getAttendanceDisplayClass($id)
    {
        $status = AttendanceStatus::find($id);
        if ($status) {
            return $status->display_class;
        }else{
            return "";
        }
    }

    public function allSubjectsList()
    {
        $subjects = Subject::Active()->orderBy('title')->pluck('title','id')->toArray();
        return array_prepend($subjects,'Select Subject','0');
    }


    public function activeState()
    {
        $state = State::select('id', 'title')->Active()->pluck('title','title')->toArray();
        return array_prepend($state,'Select State','');
    }

    public function activeAnnexures()
    {
        $annexure = Annexure::select('id', 'title')->Active()->pluck('title','title')->toArray();
        return array_prepend($annexure,'Select Annexure','');
    }

    public function activeScholarship()
    {
        return $scholarship = Scholarship::select('id', 'title')->Active()->pluck('title','id')->toArray();
       //return array_prepend($scholarship,'Select Scholarship','');
    }

    public function activePlacement()
    {
        return $placement = Placement::select('id', 'title')->Active()->pluck('title','id')->toArray();
        //return array_prepend($placement,'Select Placement','');
    }

    public function activeDegrees()
    {
       return $degrees = Degree::select('id', 'title')->Active()->get();
        //return array_prepend($degrees,'Select Degrees','');
    }

    /**
     * The validity date to print on a student's ID card.
     *
     * The rule the college uses is the session start year plus the length of the course, ending on
     * the last day of the month that course finishes in - June for HSC, December for degree and
     * honours. So a 2025-2026 admission reads:
     *
     *     HSC      2 years, June      -> 30 June 2027
     *     Degree   3 years, December  -> 31 December 2028
     *     Honours  4 years, December  -> 31 December 2029
     *
     * Only the month is stored against the department; the last day is worked out here, so nobody
     * has to remember that June has 30 days and February moves.
     *
     * A department with its two boxes empty falls back to the rule the card used before any of
     * this existed - 31 December of the year after the session ends - so a newly created
     * department cannot produce a card with no date on it.
     *
     * Kept here rather than in the card view because arithmetic on a student's identity document
     * does not belong in a template, and because a copy in the view would have to be found again
     * the next time the rule changes.
     *
     * @param  string   $sessionTitle   the batch title, e.g. '2025-2026'
     * @param  int|null $years          course length from the department
     * @param  int|null $month          1-12, the month the course ends in
     * @return string                   e.g. '30 June 2027', or '' if nothing can be worked out
     */
    public function idCardExpiryDate($sessionTitle, $years = null, $month = null)
    {
        $years = (int) $years;
        $month = (int) $month;

        /* The session title carries two years - '2025-2026'. The first is when the course began,
           and that is what the count runs from. max() would take 2026 and shift every card a
           year late, which is the mistake the old rule made. */
        $startYear = 0;
        if (preg_match_all('/(20\d{2})/', (string) $sessionTitle, $found) && count($found[1]) > 0) {
            $startYear = (int) min($found[1]);
        }

        if ($startYear > 0 && $years >= 1 && $month >= 1 && $month <= 12) {
            /* Last day of that month, whatever its length. */
            return \Carbon\Carbon::createFromDate($startYear + $years, $month, 1)
                ->endOfMonth()
                ->format('j F Y');
        }

        /* The department has not been set up. The card behaves exactly as it did before this was
           built, so switching this on cannot leave a card blank. */
        if (preg_match_all('/(20\d{2})/', (string) $sessionTitle, $found) && count($found[1]) > 0) {
            return '31 December '.((int) max($found[1]) + 1);
        }

        return '';
    }

}