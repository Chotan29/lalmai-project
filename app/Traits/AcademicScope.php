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

    public function getAcademicStatus($id)
    {
        $status = StudentStatus::find($id);
        if ($status) {
            return $status->title;
        }else{
            return "Unknown";
        }
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

}