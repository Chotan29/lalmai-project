<?php
namespace App\Traits;

use App\Models\Annexure;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\State;
use App\Models\StudentBatch;
use App\Models\StudentStatus;

trait FacultySemesterScope{

    public function activeFaculties()
    {
        $faculty = Faculty::Active()->orderBy('sorting')->pluck('faculty','id')->toArray();
         return array_prepend($faculty,'Select '.__('form_fields.student.fields.faculty'),'');
    }

    public function activeSemester()
    {
        $semester = Semester::select('id', 'semester')->Active()->pluck('semester','id')->toArray();
        return array_prepend($semester,'Select '.__('form_fields.student.fields.semester'),'');
    }

    public function activeBatch()
    {
        $studentBatch = StudentBatch::select('id', 'title')->Active()->pluck('title','id')->toArray();
        return array_prepend($studentBatch,'Select '.__('form_fields.student.fields.batch'),'');
    }

    public function activeStudentAcademicStatus()
    {
        $status = StudentStatus::Active()->orderBy('title')->pluck('title','id')->toArray();
        return array_prepend($status,'Select '.__('form_fields.student.fields.academic_status'),'');
    }

    /**
     * The small lists - departments, semesters, batches - held for the length of one request.
     *
     * These are called once per row from the list screens, and there are ten departments and a
     * handful of semesters behind hundreds of rows. On the fee collection screen that was three
     * hundred queries to answer three questions: measured at 312 queries and 3.9 seconds, of
     * which 1.9 seconds was these.
     *
     * A request holds a database connection for as long as it runs, and this account is allowed
     * twenty-five of them, so time spent here is capacity spent. Reading each list once and
     * keeping it in memory costs one query and gives the rest away free.
     *
     * Deliberately per-request and nothing longer: the array dies with the request, so a
     * department renamed in one screen is correct in the very next one. Nothing to expire and
     * nothing to go stale.
     */
    protected static $fsLookupCache = [];

    protected function fsLookup($key, callable $load)
    {
        if (!array_key_exists($key, self::$fsLookupCache)) {
            self::$fsLookupCache[$key] = $load();
        }
        return self::$fsLookupCache[$key];
    }

    /** Forget the held lists - for a long-running command that edits them as it goes. */
    public static function flushFacultySemesterCache()
    {
        self::$fsLookupCache = [];
    }

    public function getFacultyTitle($id)
    {
        $all = $this->fsLookup('faculties', function () {
            return Faculty::pluck('faculty', 'id')->all();
        });

        return $all[$id] ?? "Unknown";
    }

    public function getSemesterById($id)
    {
        $all = $this->fsLookup('semesters', function () {
            return Semester::pluck('semester', 'id')->all();
        });

        return $all[$id] ?? "";
    }

    public function getSemesterTitle($id)
    {
        $all = $this->fsLookup('semesters', function () {
            return Semester::pluck('semester', 'id')->all();
        });

        return $all[$id] ?? "Unknown";
    }

    public function getStudentBatchById($id)
    {
        $all = $this->fsLookup('batches', function () {
            return StudentBatch::pluck('title', 'id')->all();
        });

        return $all[$id] ?? "Unknown";
    }



}