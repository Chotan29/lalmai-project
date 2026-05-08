<?php

/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\SuperSuit;

use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Image, URL;
use OwenIt\Auditing\Models\Audit;
use ViewHelper;

class OnClickActionController extends CollegeBaseController
{
    protected $base_route = 'super-suit.cleaner';
    protected $view_path = 'super-suit.cleaner';
    protected $panel = 'One Click Clean';
    protected $filter_query = [];

    public function __construct()
    {
    }

    public function cleaner(Request $request)
    {
        return view(parent::loadDataToView($this->view_path.'.index')/*, compact('data')*/);

    }

    public function clearAllCache(Request $request)
    {
        Cache::flush();
        request()->session()->flash("message_success", "Cache Clear Now.");
        return back();
    }

    public function cacheClear(Request $request)
    {
        Artisan::call('cache:clear');
        request()->session()->flash("message_success", "Cache Cache Clean Successfully.");
        return back();
    }
    public function routeClear(Request $request)
    {
        Artisan::call('route:clear');
        request()->session()->flash("message_success", "Route Cache Clean Successfully.");
        return back();
    }
    public function configClear(Request $request)
    {
        Artisan::call('config:clear');
        request()->session()->flash("message_success", "Configuration Cache Clean Successfully.");
        return back();
    }
    public function viewClear(Request $request)
    {
        Artisan::call('view:clear');
        request()->session()->flash("message_success", "View Cache Clean Successfully.");
        return back();
    }


    public function clearUserActivity(Request $request)
    {
        Audit::truncate();
        request()->session()->flash("message_success", "User History/Log Clean Successfully.");
        return back();
    }

    public function clearStudentOperationalData(Request $request)
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');

        DB::disableQueryLog();

        $deletedSummary = [];
        $studentRoleIds = $this->tableExists('roles')
            ? DB::table('roles')->whereIn('name', ['student', 'guardian'])->pluck('id')->all()
            : [];

        if (!empty($studentRoleIds) && $this->tableExists('users')) {
            $deletedSummary['users'] = DB::table('users')->whereIn('role_id', $studentRoleIds)->delete();
        }

        $deletedSummary['documents'] = $this->deleteTableRows('documents', function ($query) {
            $query->where('member_type', 'student');
        });

        $deletedSummary['notes'] = $this->deleteTableRows('notes', function ($query) {
            $query->where('member_type', 'student');
        });

        $libraryMemberIds = $this->tableExists('library_members')
            ? DB::table('library_members')->where('user_type', 1)->pluck('id')->all()
            : [];
        if (!empty($libraryMemberIds) && $this->tableExists('book_issues')) {
            $deletedSummary['book_issues'] = DB::table('book_issues')->whereIn('member_id', $libraryMemberIds)->delete();
        }
        $deletedSummary['library_members'] = $this->deleteTableRows('library_members', function ($query) {
            $query->where('user_type', 1);
        });

        $transportUserIds = $this->tableExists('transport_users')
            ? DB::table('transport_users')->where('user_type', 1)->pluck('id')->all()
            : [];
        if (!empty($transportUserIds) && $this->tableExists('transport_histories')) {
            $deletedSummary['transport_histories'] = DB::table('transport_histories')->whereIn('travellers_id', $transportUserIds)->delete();
        }
        $deletedSummary['transport_users'] = $this->deleteTableRows('transport_users', function ($query) {
            $query->where('user_type', 1);
        });

        $residentIds = $this->tableExists('residents')
            ? DB::table('residents')->where('user_type', 1)->pluck('id')->all()
            : [];
        if (!empty($residentIds) && $this->tableExists('resident_histories')) {
            $deletedSummary['resident_histories'] = DB::table('resident_histories')->whereIn('residents_id', $residentIds)->delete();
        }
        $deletedSummary['residents'] = $this->deleteTableRows('residents', function ($query) {
            $query->where('user_type', 1);
        });

        $guardianIds = $this->tableExists('student_guardians')
            ? DB::table('student_guardians')->pluck('guardians_id')->filter()->unique()->values()->all()
            : [];

        $deletedSummary['student_guardians'] = $this->deleteTableRows('student_guardians');

        if (!empty($guardianIds) && $this->tableExists('guardian_details')) {
            $deletedSummary['guardian_details'] = DB::table('guardian_details')->whereIn('id', $guardianIds)->delete();
        }

        $modelTables = [
            'academic_infos' => \App\Models\AcademicInfo::class,
            'addressinfos' => \App\Models\Addressinfo::class,
            'parent_details' => \App\Models\ParentDetail::class,
            'online_payments' => \App\Models\OnlinePayment::class,
            'student_subject' => \App\Models\StudentSubject::class,
            'student_annexures' => \App\Models\StudentAnnexure::class,
            'student_extra_infos' => \App\Models\StudentExtraInfo::class,
            'student_scholarships' => \App\Models\StudentScholarship::class,
            'student_placements' => \App\Models\StudentPlacement::class,
            'student_degrees' => \App\Models\StudentDegree::class,
            'assignment_answers' => \App\Models\AssignmentAnswer::class,
            'exam_mark_ledgers' => \App\Models\ExamMarkLedger::class,
            'fee_collections' => \App\Models\FeeCollection::class,
            'fee_masters' => \App\Models\FeeMaster::class,
            'certificate_histories' => \App\Models\CertificateHistory::class,
            'attendance_certificates' => \App\Models\AttendanceCertificate::class,
            'bonafide_certificates' => \App\Models\BonafideCertificate::class,
            'course_completion_certificates' => \App\Models\CourseCompletionCertificate::class,
            'transfer_certificates' => \App\Models\TransferCertificate::class,
            'character_certificates' => \App\Models\CharacterCertificate::class,
            'm_o_i_certificates' => \App\Models\MOICertificate::class,
            'nirgam_utara_certificates' => \App\Models\NirgamUtaraCertificate::class,
            'provisional_certificates' => \App\Models\ProvisionalCertificate::class,
            'testimonial_certificates' => \App\Models\TestimonialCertificate::class,
            'transcript_certificates' => \App\Models\TranscriptCertificate::class,
        ];

        foreach ($modelTables as $label => $modelClass) {
            $deletedSummary[$label] = $this->deleteAllModelRows($modelClass);
        }

        $deletedSummary['attendances'] = $this->deleteStudentAttendances();

        $deletedSummary['subject_attendances'] = $this->deleteTableRows('subject_attendances');
        $deletedSummary['students'] = $this->deleteTableRows('students');

        $totalDeleted = array_sum(array_map(function ($count) {
            return (int) $count;
        }, $deletedSummary));

        if ($totalDeleted === 0) {
            request()->session()->flash("message_info", "No student operational data found to clean.");
            return back();
        }

        request()->session()->flash("message_success", $totalDeleted . " operational records cleaned successfully. Master/setup data was preserved.");
        return back();
    }

    protected function tableExists($table)
    {
        return Schema::hasTable($table);
    }

    protected function deleteAllModelRows($modelClass)
    {
        if (!class_exists($modelClass)) {
            return 0;
        }

        $instance = new $modelClass();
        if (!$this->tableExists($instance->getTable())) {
            return 0;
        }

        return $modelClass::query()->delete();
    }

    protected function deleteStudentAttendances()
    {
        if (!$this->tableExists('attendances')) {
            return 0;
        }

        $query = DB::table('attendances');

        if (Schema::hasColumn('attendances', 'attendable_type')) {
            return $query->whereIn('attendable_type', [\App\Models\Student::class, 'student'])->delete();
        }

        if (Schema::hasColumn('attendances', 'attendees_type')) {
            return $query->where('attendees_type', 1)->delete();
        }

        return 0;
    }

    protected function deleteTableRows($table, $callback = null)
    {
        if (!$this->tableExists($table)) {
            return 0;
        }

        $query = DB::table($table);
        if ($callback) {
            $callback($query);
        }

        return $query->delete();
    }

}
