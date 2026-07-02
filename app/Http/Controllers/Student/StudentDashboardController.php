<?php

namespace App\Http\Controllers\Student;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentDashboardController extends Controller
{
    /** Find first existing column on table from candidates */
    private function col(string $table, array $cands): ?string
    {
        foreach ($cands as $c) if (Schema::hasColumn($table, $c)) return $c;
        return null;
    }

    /** Return id=>label map as JSON */
    private function jsonMap($collection)
    {
        return response()->json($collection ?? []);
    }

    /** GET /get-departments?department_head_id=...  → {id: "Department Name", ...} */
    public function getDepartments(Request $r)
    {
        if (!Schema::hasTable('departments')) return $this->jsonMap([]);

        $nameCol = $this->col('departments', ['department','title','name']);
        $q = DB::table('departments')->select($nameCol ?: DB::raw("'Department' as department"), 'id');

        // Filter by department head if that column exists
        $headId = $r->query('department_head_id');
        if ($headId && Schema::hasColumn('departments','department_head_id')) {
            $q->where('department_head_id', $headId);
        }

        return $this->jsonMap($q->pluck($nameCol ?: 'department', 'id'));
    }

    /** GET /get-faculties?department_id=...  → {id: "Faculty Name", ...} */
    public function getFaculties(Request $r)
    {
        // Preferred: use department_programs map → faculties
        if (Schema::hasTable('department_programs') && Schema::hasTable('faculties')) {
            $depId = $r->query('department_id');
            $facNameCol = $this->col('faculties', ['faculty','title','name']);

            $dpDeptCol = $this->col('department_programs', ['department_id','dept_id']);
            $dpFacCol  = $this->col('department_programs', ['faculty_id','program_id']);

            if ($dpDeptCol && $dpFacCol && $facNameCol) {
                $facIds = DB::table('department_programs')
                    ->when($depId, fn($q)=>$q->where($dpDeptCol, $depId))
                    ->distinct()->pluck($dpFacCol);

                $rows = DB::table('faculties')->whereIn('id',$facIds)->pluck($facNameCol,'id');
                if ($rows->count()) return $this->jsonMap($rows);
            }
        }

        // Fallback: plain faculties table (optionally with department_id if present)
        if (Schema::hasTable('faculties')) {
            $nameCol = $this->col('faculties', ['faculty','title','name']);
            $q = DB::table('faculties')->select($nameCol ?: DB::raw("'Faculty' as faculty"), 'id');

            $depId = $r->query('department_id');
            if ($depId && Schema::hasColumn('faculties','department_id')) {
                $q->where('department_id', $depId);
            }

            return $this->jsonMap($q->pluck($nameCol ?: 'faculty', 'id'));
        }

        return $this->jsonMap([]);
    }

    /** GET /get-semesters?faculty_id=...  → {id: "Semester Name", ...} */
    public function getSemesters(Request $r)
    {
        // Preferred: from department_programs → semesters
        if (Schema::hasTable('department_programs') && Schema::hasTable('semesters')) {
            $facId = $r->query('faculty_id');

            $dpFacCol = $this->col('department_programs', ['faculty_id','program_id']);
            $dpSemCol = $this->col('department_programs', ['semester_id','sem_id']);
            $semName  = $this->col('semesters', ['semester','title','name']);

            if ($dpFacCol && $dpSemCol && $semName) {
                $semIds = DB::table('department_programs')
                    ->when($facId, fn($q)=>$q->where($dpFacCol, $facId))
                    ->distinct()->pluck($dpSemCol);

                $rows = DB::table('semesters')->whereIn('id',$semIds)->pluck($semName,'id');
                if ($rows->count()) return $this->jsonMap($rows);
            }
        }

        // Fallback: plain semesters table (optionally with faculty_id if it exists)
        if (Schema::hasTable('semesters')) {
            $semName = $this->col('semesters', ['semester','title','name']);
            $q = DB::table('semesters')->select($semName ?: DB::raw("'Semester' as semester"), 'id');

            $facId = $r->query('faculty_id');
            if ($facId && Schema::hasColumn('semesters','faculty_id')) {
                $q->where('faculty_id', $facId);
            }

            return $this->jsonMap($q->pluck($semName ?: 'semester', 'id'));
        }

        return $this->jsonMap([]);
    }

    /** GET /get-batches?semester_id=...  → {id: "Batch Title", ...}  (FIXES your dump) */
    public function getBatches(Request $r)
    {
        if (!Schema::hasTable('student_batches')) return $this->jsonMap([]);

        $titleCol = $this->col('student_batches', ['title','name']);
        $q = DB::table('student_batches')->select($titleCol ?: DB::raw("'Batch' as title"), 'id');

        // ✅ Filter by semester using synonyms
        $semId = $r->query('semester_id');
        $sbSemCol = $this->col('student_batches', ['semester_id','sem_id','semester']);
        if ($semId && $sbSemCol) {
            $q->where($sbSemCol, $semId);
        }

        return $this->jsonMap($q->pluck($titleCol ?: 'title', 'id'));
    }
}
