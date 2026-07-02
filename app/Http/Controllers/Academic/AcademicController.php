<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\CollegeBaseController;
use App\Models\DepartmentHead;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\StudentBatch;
use App\Models\Subject;
use Illuminate\Http\Request;

class AcademicController extends CollegeBaseController
{
    /**
     * Get departments for a department head (AJAX)
     */
    public function getDepartments(Request $request)
    {
        $request->validate(['department_head_id' => 'required|integer|exists:department_heads,id']);

        $departments = Department::whereHas('heads', function($q) use ($request) {
                $q->where('department_heads.id', $request->department_head_id);
            })
            ->pluck('department', 'id');

        return response()->json($departments);
    }

    /**
     * Get faculties for a department (AJAX)
     */
    public function getFaculties(Request $request)
    {
        $request->validate(['department_id' => 'required|integer|exists:departments,id']);

        $faculties = Faculty::whereHas('departments', function($q) use ($request) {
                $q->where('departments.id', $request->department_id);
            })
            ->pluck('faculty', 'id');

        return response()->json($faculties);
    }

    /**
     * Get semesters for a faculty (AJAX)
     * Excludes semesters where title IN ('PASSED', 'CONTINUE')
     * Adjust the column if your status lives in 'semester' instead of 'title'.
     */
    public function getSemesters(Request $request)
    {
        $request->validate(['faculty_id' => 'required|integer|exists:faculties,id']);

        $semesters = Semester::whereHas('faculties', function($q) use ($request) {
                $q->where('faculties.id', $request->faculty_id);
            })
            ->when(schema_has_column('semesters','title'), function($q){
                $q->whereNotIn('title', ['PASSED','CONTINUE']);
            }, function($q){
                // fallback if 'title' doesn't exist, try excluding on 'semester' label
                $q->whereNotIn('semester', ['PASSED','CONTINUE']);
            })
            ->pluck('semester', 'id');

        // Live fallback: some faculties may miss faculty_semester mapping,
        // but schedules/students already contain valid semester links.
        if ($semesters->isEmpty()) {
            $scheduleSemesterIds = \DB::table('exam_schedules')
                ->where('faculty_id', (int) $request->faculty_id)
                ->distinct()
                ->pluck('semesters_id')
                ->filter();

            if ($scheduleSemesterIds->isNotEmpty()) {
                $semesters = Semester::whereIn('id', $scheduleSemesterIds)
                    ->when(schema_has_column('semesters','title'), function($q){
                        $q->whereNotIn('title', ['PASSED','CONTINUE']);
                    }, function($q){
                        $q->whereNotIn('semester', ['PASSED','CONTINUE']);
                    })
                    ->pluck('semester', 'id');
            }
        }

        if ($semesters->isEmpty()) {
            $studentSemesterIds = \DB::table('students')
                ->where('faculty', (int) $request->faculty_id)
                ->distinct()
                ->pluck('semester')
                ->filter();

            if ($studentSemesterIds->isNotEmpty()) {
                $semesters = Semester::whereIn('id', $studentSemesterIds)
                    ->when(schema_has_column('semesters','title'), function($q){
                        $q->whereNotIn('title', ['PASSED','CONTINUE']);
                    }, function($q){
                        $q->whereNotIn('semester', ['PASSED','CONTINUE']);
                    })
                    ->pluck('semester', 'id');
            }
        }

        return response()->json($semesters);
    }

    /**
     * Get batches for a semester (AJAX)
     */
    public function getBatches(Request $request)
    {
        // $request->validate(['semester_id' => 'required|integer|exists:semesters,id']);

        // $batches = StudentBatch::where('semester_id', $request->semester_id)
        //     ->pluck('title', 'id');

        $batches = StudentBatch::pluck('title', 'id');

        return response()->json($batches);
    }

    /**
     * Get subjects for a semester (AJAX)
     * Uses Semester->subjects() if exists; falls back to Subject::where('semester_id',..)
     */
    public function getSubjects(Request $request)
    {
        $request->validate(['semester_id' => 'required|integer|exists:semesters,id']);

        $semester = Semester::find($request->semester_id);

        if ($semester && method_exists($semester, 'subjects')) {
            $subjects = $semester->subjects()->pluck('subjects.title', 'subjects.id');
        } else {
            $subjects = Subject::where('semester_id', $request->semester_id)
                ->pluck('title', 'id');
        }

        return response()->json($subjects);
    }

    // public function getSubjects(Request $request)
    // {
    //     $request->validate(['semester_id' => 'required|integer|exists:semesters,id']);

    //     $semester = Semester::findOrFail($request->semester_id);

    //     if (method_exists($semester, 'subjects')) {
    //         $rows = $semester->subjects()
    //             ->select('subjects.id', 'subjects.title')
    //             ->orderByRaw('LOWER(subjects.title) ASC')
    //             ->get();
    //     } else {
    //         $rows = \App\Models\Subject::query()
    //             ->select('id', 'title')
    //             ->where('semester_id', $request->semester_id)
    //             ->orderByRaw('LOWER(title) ASC')
    //             ->get();
    //     }

    //     // Final natural, case-insensitive safeguard (handles dashes, parentheses, etc.)
    //     $rows = $rows->sort(fn($a,$b) => strnatcasecmp($a->title, $b->title))->values();

    //     // Return an array of {id,title} to preserve order in the client
    //     return response()->json(
    //         $rows->map(fn($s) => ['id' => (int)$s->id, 'title' => $s->title])
    //     );
    // }


}

/**
 * Tiny helper: avoid crashing if the column check runs on older Laravel
 */
if (!function_exists('schema_has_column')) {
    function schema_has_column($table, $column) {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
