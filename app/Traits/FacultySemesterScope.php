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

    public function getFacultyTitle($id)
    {
        $faculty = Faculty::find($id);
        if ($faculty) {
            return $faculty->faculty;
        }else{
            return "Unknown";
        }
    }

    public function getSemesterById($id)
    {
        $semester = Semester::find($id);
        if ($semester) {
            return $semester->semester;
        }else{
            return "";
        }
    }

    public function getSemesterTitle($id)
    {
        $semester = Semester::find($id);
        if ($semester) {
            return $semester->semester;
        }else{
            return "Unknown";
        }
    }

    public function getStudentBatchById($id)
    {
        $batch = StudentBatch::find($id);
        if ($batch) {
            return $batch->title;
        }else{
            return "Unknown";
        }
    }



}