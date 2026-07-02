<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasRegNo;

class Staff extends BaseModel
{
    use HasRegNo;
    protected $table = 'staff';
    protected $fillable = ['created_by', 'last_updated_by', 'reg_no', 'join_date', 'designation', 'first_name',  'middle_name', 'last_name',
        'father_name', 'mother_name', 'date_of_birth', 'gender', 'blood_group', 'nationality','mother_tongue',
        'national_id_1', 'national_id_2', 'national_id_3', 'national_id_4',
        'home_phone', 'mobile_1', 'mobile_2', 'email',
        'address', 'state', 'country','postal_code',
        'temp_address', 'temp_state', 'temp_country','temp_postal_code',
        'qualification',  'basic_salary', 'date_of_relieving', 'date_of_rejoin', 'bank_name', 'bank_account_number', 'bank_code',
        'experience', 'experience_info', 'other_info', 'staff_image', 'status'];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->middle_name} {$this->last_name}";
    }

    public function staffNotes()
    {
        return $this->hasMany(Note::class,'member_id','id')->where('member_type','=','staff');
    }

    public function staffDocuments()
    {
        return $this->hasMany(Document::class,'member_id','id')->where('member_type','=','staff');
    }
    public function attendances()
    {
        return $this->morphMany(Attendance::class, 'attendable');
    }

    public function teachingSlots()
    {
        return $this->hasMany(ClassRoutineDetail::class, 'teacher_id');
    }



    public function payrollMaster()
    {
        return $this->hasMany(PayrollMaster::class, 'staff_id');
    }

    public function paySalary()
    {
        return $this->hasMany(SalaryPay::class, 'staff_id');
    }

    //Library Member
    public function libraryMember()
    {
        return $this->hasMany(LibraryMember::class,'member_id','id')->where('user_type','=',2);
    }

    //transport User
    public function transportUser()
    {
        return $this->hasMany(TransportUser::class,'member_id','id')->where('user_type','=',2);
    }

    //Hostel Resident
    public function hostelResident()
    {
        return $this->hasMany(Resident::class,'member_id','id')->where('user_type','=',2);
    }

    //Regular Attendance
    public function regularAttendance()
    {
        return $this->morphMany(Attendance::class, 'attendable');
    }

}
