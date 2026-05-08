<?php
namespace App\Exports;

use App\Models\Staff;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StaffsExport implements FromView
{
    protected $ids;

    public function __construct($dataIds)
    {
        $this->ids = $dataIds;
    }

    public function view(): View
    {
        $staffs = Staff::select('id','reg_no', 'join_date', 'designation', 'first_name',  'middle_name', 'last_name',
            'father_name', 'mother_name', 'date_of_birth', 'gender', 'blood_group', 'nationality','mother_tongue', 'address', 'state', 'country',
            'temp_address', 'temp_state', 'temp_country', 'home_phone', 'mobile_1', 'mobile_2', 'email', 'qualification',
            'experience', 'experience_info', 'other_info','status')
            ->whereIn('id',$this->ids)
            ->get();


        return view('exports.staff', [
            'staffs' => $staffs
        ]);
    }
}