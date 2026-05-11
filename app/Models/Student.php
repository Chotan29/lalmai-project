<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Concerns\HasRegNo;

class Student extends BaseModel
{
    use HasRegNo;

    protected $fillable = ['created_by', 'last_updated_by', 'reg_no', 'reg_date', 'university_reg','faculty','semester','batch',
        'academic_status', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender', 'blood_group',
        'nationality', 'national_id_1', 'national_id_2', 'national_id_3','national_id_4',
        'religion', 'caste','mother_tongue', 'email', 'home_phone', 'mobile_1', 'mobile_2', 'extra_info', 'student_image','student_signature','status',

        'reg_fee','sbi_collect_no','bank_ref_no','payment_date','university_enrollment_no', 'admission_date','admission_no',
        'admission_payment_ref_no','admission_course_fee','national_id_1','national_id_2', 'special_category','weightage_claim',
        'student_type', 'registration_payment_status',
        ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }


    public function address()
    {
        return $this->hasOne(Addressinfo::class,'students_id', 'id');
    }

    public function parents()
    {
        return $this->hasOne(ParentDetail::class, 'students_id', 'id');
    }

    public function guardian()
    {
        return $this->hasOne(StudentGuardian::class, 'students_id', 'id');
    }

    /* public function guardian()
     {
         return $this->belongsTo(StudentGuardian::class);
     }*/

    public function academicInfo()
    {
        return $this->hasMany(AcademicInfo::class, 'students_id', 'id');
    }

    public function majorSubject()
    {
        return $this->hasMany(StudentSubject::class, 'students_id', 'id');
    }

    public function studentSubjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subject', 'students_id', 'subjects_id');
    }

    public function annexure()
    {
        return $this->hasMany(StudentAnnexure::class, 'students_id', 'id');
    }

    public function studentExtraInfo()
    {
        return $this->hasMany(StudentExtraInfo::class, 'students_id', 'id');
    }

    public function studentScholarship()
    {
        return $this->hasOne(StudentScholarship::class, 'students_id', 'id');
    }

    public function studentPlacement()
    {
        return $this->hasOne(StudentPlacement::class, 'students_id', 'id');
    }

//    public function studentDegrees()
//    {
//        //return $this->belongsToMany(StudentDegree::class, 'student_degrees','students_id', 'id');
//        return $this->belongsToMany(StudentDegree::class, 'student_degrees','students_id', 'id');
//    }
    public function studentDegrees()
    {
       // return $this->hasMany(StudentDegree::class, 'students_id', 'id');
        return $this->hasMany(StudentDegree::class, 'students_id', 'id');
    }

    public function studentNotes()
    {
        return $this->hasMany(Note::class,'member_id','id')->where('member_type','=','student');
    }

    public function studentDocuments()
    {
        return $this->hasMany(Document::class,'member_id','id')->where('member_type','=','student');
    }

    // public function feeMaster()  // Singular
    // {
    //     return $this->hasMany(FeeMaster::class, 'students_id', 'id');
    // }
    
    // public function feeCollect()
    // {
    //     return $this->hasMany(FeeCollection::class, 'students_id', 'id');
    // }

    // app/Models/Student.php
    public function feeMaster()
    {
        return $this->hasMany(FeeMaster::class, 'students_id', 'id');
    }

    public function feeCollect()
    {
        return $this->hasMany(FeeCollection::class, 'students_id', 'id')->where('status', 1);
    }

    public function onlinePayments()
    {
        return $this->hasMany(OnlinePayment::class, 'students_id', 'id');
    }


    public function markLedger()
    {
        return $this->hasMany(ExamMarkLedger::class, 'students_id', 'id');
    }

    //assignment Answer
    public function assignmentAnswers()
    {
        return $this->hasMany(AssignmentAnswer::class,'students_id','id');

    }

    //Library Member
    public function libraryMember()
    {
        return $this->hasMany(LibraryMember::class,'member_id','id')->where('user_type','=',1);
    }

    //Library Book Requested by Member
    /*public function bookRequest()
    {
        return $this->hasMany(BookRequest::class,'member_id','id');
    }*/

    //transport User
    public function transportUser()
    {
        return $this->hasMany(TransportUser::class,'member_id','id')->where('user_type','=',1);
    }

    //Hostel Resident
    public function hostelResident()
    {
        return $this->hasMany(Resident::class,'member_id','id')->where('user_type','=',1);
    }

    //Regular Attendance
    public function regularAttendance()
    {
        return $this->morphMany(Attendance::class, 'attendable');
    }

    // //Regular Attendance
    // public function subjectAttendance()
    // {
    //     return $this->hasMany(SubjectAttendance::class,'link_id','id');
    // }

    // public function attendances()
    // {
    //     return $this->morphMany(\App\Models\Attendance::class, 'attendable');
    // }


    //certificates
    public function certificateHistory()
    {
        return $this->hasMany(CertificateHistory::class, 'students_id', 'id');
    }

    public function attendanceCertificate()
    {
        return $this->hasOne(AttendanceCertificate::class, 'students_id', 'id');
    }

    public function transferCertificate()
    {
        return $this->hasOne(TransferCertificate::class, 'students_id', 'id');
    }

    public function characterCertificate()
    {
        return $this->hasOne(CharacterCertificate::class, 'students_id', 'id');
    }

    public function bonafideCertificate()
    {
        return $this->hasOne(BonafideCertificate::class, 'students_id', 'id');
    }

    public function courseCompletionCertificate()
    {
        return $this->hasOne(CourseCompletionCertificate::class, 'students_id', 'id');
    }

    public function nirgamUtaraCertificate()
    {
        return $this->hasOne(NirgamUtaraCertificate::class, 'students_id', 'id');
    }

    public function provisionalCertificate()
    {
        return $this->hasOne(ProvisionalCertificate::class, 'students_id', 'id');
    }

    public function testimonialCertificate()
    {
        return $this->hasOne(TestimonialCertificate::class, 'students_id', 'id');
    }

    public function MOICertificate()
    {
        return $this->hasOne(MediumOfInstructionCertificate::class, 'students_id', 'id');
    }


    public function transcriptCertificate()
    {
        return $this->hasOne(TranscriptCertificate::class, 'students_id', 'id');
    }


    public function attendances()
    {
        return $this->morphMany(Attendance::class, 'attendable');
    }

    public function subjectAttendances()
    {
        return $this->hasMany(SubjectAttendance::class, 'student_id');
    }

    /**
     * Helper: fetch today's routine slots for this student's cohort.
     */
    public function todaysSlots()
    {
        $faculty  = $this->faculty;
        $semester = $this->semester;
        $batch    = $this->batch;

        if (!$faculty || !$semester || !$batch) {
            return collect();
        }

        $routines = ClassRoutine::forCohort($faculty, $semester, $batch)->pluck('id');
        if ($routines->isEmpty()) return collect();

        return ClassRoutineDetail::query()
            ->whereIn('class_routine_id', $routines->all())
            ->today()
            ->get();
    }


}
