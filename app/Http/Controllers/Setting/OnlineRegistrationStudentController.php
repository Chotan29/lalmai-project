<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\CollegeBaseController;
use App\Models\Student;
use App\Models\OnlineRegistrationSetting;
use App\Models\OnlinePayment;
use App\Models\PaymentRefund;
use App\Services\Admission\AdmissionCancellation;
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

        /*
         * Count stats.
         *
         * Somebody who paid and then did not take admission is still a row here - the application,
         * the photo and the payment all happened - but they are not a student of this college and
         * counting them would overstate every one of these figures. admitted() leaves them out;
         * lifting the mark brings them straight back.
         */
        $data['total_online_students'] = Student::whereNotNull('student_type')->admitted()->count();
        $data['new_students'] = Student::where('student_type', 'new')->admitted()->count();
        $data['old_students'] = Student::where('student_type', 'old')->admitted()->count();
        $data['active_students'] = Student::whereNotNull('student_type')->admitted()->where('status', 1)->count();

        /* Shown separately rather than hidden, so the number is accounted for and not just missing. */
        $data['cancelled_students'] = Student::whereNotNull('student_type')->admissionCancelled()->count();

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

        /* Money in, money back, and what is left - the three figures the office actually asks for. */
        $admission = app(AdmissionCancellation::class);
        $data['refunds']         = PaymentRefund::where('students_id', $id)->orderBy('id', 'desc')->get();
        $data['paid_total']      = $admission->paidTotal($id);
        $data['refunded_total']  = $admission->refundedTotal($id);
        $data['refundable']      = max(0, $data['paid_total'] - $data['refunded_total']);
        $data['refund_methods']  = PaymentRefund::methods();

        return view(parent::loadDataToView($this->view_path.'.show'), compact('data'));
    }

    /**
     * The admission did not go ahead.
     *
     * A mark, not a deletion. The application, the photo and the payment all happened and the
     * college may have to show that later; what changes is that the person stops being counted as
     * a student and comes off the attendance device.
     */
    public function cancelAdmission(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $done = app(AdmissionCancellation::class)->cancel(
            $student,
            $request->input('note', ''),
            auth()->id()
        );

        if (!$done) {
            $this->message = 'This admission was already marked as not taken.';
        } else {
            $this->message = 'Marked as admission not taken. The student is out of the counts and off the device.';
        }

        return redirect()->route($this->base_route . '.show', $id);
    }

    /** Put them back exactly as they were. */
    public function restoreAdmission($id)
    {
        $student = Student::findOrFail($id);

        app(AdmissionCancellation::class)->restore($student, auth()->id());

        $this->message = 'Admission restored. The student is counted again and goes back on the device.';

        return redirect()->route($this->base_route . '.show', $id);
    }

    /**
     * Record money handed back.
     *
     * The payment row is left alone - the money really did come in that day. This is a second row
     * saying it went out again, so the month's collection is the difference and both halves can
     * still be shown to anyone who asks.
     */
    public function storeRefund(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $result = app(AdmissionCancellation::class)->refund($student, [
            'amount'            => $request->input('amount'),
            'date'              => $request->input('date') ?: now()->toDateString(),
            'method'            => $request->input('method', 'cash'),
            'ref_no'            => $request->input('ref_no'),
            'note'              => $request->input('note'),
            'online_payment_id' => $request->input('online_payment_id'),
        ], auth()->id());

        if (empty($result['ok'])) {
            $this->error = $result['message'] ?? 'The refund could not be recorded.';
        } else {
            $this->message = 'Refund recorded, voucher ' . $result['refund']->voucher_no . '.';
        }

        return redirect()->route($this->base_route . '.show', $id);
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

        /* Fee comes from the student's department (Faculty/Program row). */
        $fee = \App\Models\OnlineRegistrationProgram::resolveFee(
            $student->faculty,
            $student->semester,
            $student->student_type === 'old' ? 'old' : 'new'
        );

        if(!$fee || $fee <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Registration fee is not configured for this department. '
                    . 'Please set it in Online Registration Setting > Program Management.'
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
