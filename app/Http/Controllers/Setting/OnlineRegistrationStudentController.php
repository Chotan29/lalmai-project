<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\CollegeBaseController;
use App\Models\Student;
use App\Models\OnlineRegistrationSetting;
use App\Models\OnlinePayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OnlineRegistrationStudentController extends CollegeBaseController
{
    protected $base_route = 'setting.online-registration-student';
    protected $view_path = 'setting.online-registration-student';
    protected $panel = 'Setting';

    /**
     * List all students registered through online registration
     */
    public function index(Request $request)
    {
        $data = [];

        // Query students registered online (have student_type field)
        $query = Student::whereNotNull('student_type')
            ->orderBy('created_at', 'desc');

        // Filters
        if($request->has('student_type') && $request->get('student_type')) {
            $query->where('student_type', $request->student_type);
            $this->filter_query['student_type'] = $request->student_type;
        }

        if($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->status);
            $this->filter_query['status'] = $request->status;
        }

        if($request->has('search') && $request->get('search')) {
            $search = '%'.$request->get('search').'%';
            $query->where(function($q) use($search) {
                $q->where('reg_no', 'like', $search)
                  ->orWhere('first_name', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('mobile_1', 'like', $search);
            });
            $this->filter_query['search'] = $request->get('search');
        }

        $data['students'] = $query->paginate(25);

        // Add payment info for each student
        foreach($data['students'] as $student) {
            $student->latest_payment = OnlinePayment::where('students_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        // Count stats
        $data['total_online_students'] = Student::whereNotNull('student_type')->count();
        $data['new_students'] = Student::where('student_type', 'new')->count();
        $data['old_students'] = Student::where('student_type', 'old')->count();
        $data['active_students'] = Student::whereNotNull('student_type')->where('status', 1)->count();

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    /**
     * View student details
     */
    public function show($id)
    {
        $data = [];
        
        $data['student'] = Student::findOrFail($id);

        if(!$data['student']->student_type) {
            $this->error = "This student was not registered through online registration.";
            return redirect()->route($this->base_route.'.index');
        }

        $data['payments'] = OnlinePayment::where('students_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Try to get fees if the relationship exists
        $data['fees'] = [];
        try {
            $data['fees'] = $data['student']->feeCollect()
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            // If relationship doesn't exist, just set empty array
            $data['fees'] = [];
        }

        return view(parent::loadDataToView($this->view_path.'.show'), compact('data'));
    }

    /**
     * Initiate payment for a student (admin-initiated)
     */
    public function initiatePayment(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        if(!$student->student_type) {
            return response()->json([
                'success' => false,
                'message' => 'This student was not registered through online registration.'
            ], 422);
        }

        // Get registration setting
        $setting = OnlineRegistrationSetting::where('status', 'active')
            ->orWhere('status', 1)
            ->first() ?? OnlineRegistrationSetting::first();

        if(!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Registration settings not found. Please configure registration settings first.'
            ], 422);
        }

        // Get the appropriate fee
        $fee = $student->student_type === 'new' 
            ? $setting->new_student_registration_fee 
            : $setting->old_student_registration_fee;

        if(!$fee || $fee <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Registration fee is not configured for this student type.'
            ], 422);
        }

        // Check if payment already completed
        $existingPayment = OnlinePayment::where('students_id', $id)
            ->where('payment_status', 'completed')
            ->first();

        if($existingPayment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment already completed for this student on ' . $existingPayment->date . '.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'student_id' => $id,
            'student_name' => $student->first_name . ' ' . $student->last_name,
            'student_type' => $student->student_type,
            'fee' => $fee,
            'message' => 'Ready to process payment. Student: ' . $student->first_name . ', Amount: ' . number_format($fee, 2)
        ]);
    }
}
