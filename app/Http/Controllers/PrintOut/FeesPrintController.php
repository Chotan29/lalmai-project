<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\PrintOut;

use App\Http\Controllers\CollegeBaseController;
use App\Models\FeeCollection;
use App\Models\FeeMaster;
use App\Models\Student;
use App\Models\OnlinePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use view, URL;
use ViewHelper;
class FeesPrintController extends CollegeBaseController
{
    protected $base_route = 'account.fees.print';
    protected $view_path = 'account.fees.print';
    protected $panel = 'Fees Collection Receipt';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function printMaster(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        $data['fee_master'] = FeeMaster::find($id);

        $data['student'] = $data['fee_master']->students()
                            ->select('students.id','students.reg_no','students.university_reg', 'students.first_name','students.middle_name','students.last_name',
                                'students.faculty','students.batch','students.semester','pd.father_first_name','pd.father_middle_name','pd.father_last_name','sb.title')
                            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
                            ->join('student_batches as sb','sb.id','=', 'students.batch')
                            ->first();


        /*total Calculation on Table Foot*/
        $data['fee_master']->discount = $data['fee_master']->feeCollect()->sum('discount');
        $data['fee_master']->fine = $data['fee_master']->feeCollect()->sum('fine');
        $data['fee_master']->paid_amount = $data['fee_master']->feeCollect()->sum('paid_amount');
        $data['fee_master']->balance =
            ($data['fee_master']->fee_amount - ($data['fee_master']->paid_amount + $data['fee_master']->discount))+ $data['fee_master']->fine;

        return view(parent::loadDataToView('print.student-fee.master'), compact('data'));
    }

    public function printSelectedMaster(Request $request)
    {
        $id = decrypt($request->studentId);
        $today = Carbon::today();
        $data['student'] = Student::select('students.id','students.reg_no','students.university_reg', 'students.first_name','students.middle_name','students.last_name',
            'students.faculty','students.batch','students.semester','pd.father_first_name','pd.father_middle_name','pd.father_last_name','sb.title')
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('student_batches as sb','sb.id','=', 'students.batch')
            ->find($id);

        $data['fee_master'] = $data['student']->feeMaster()->whereIn('id',$request->chkIds)->orderBy('fee_due_date','desc')->get();
        $data['fee_collection'] = $data['student']->feeCollect()->get();

        /*total Calculation on Table Foot*/
        $data['fee_master']->fee_amount = $data['fee_master']->sum('fee_amount');
        $data['fee_master']->discount = $data['student']->feeCollect()->whereIn('fee_masters_id',$request->chkIds)->sum('discount');

        $data['fee_master']->fine = $data['student']->feeCollect()->whereIn('fee_masters_id',$request->chkIds)->sum('fine');
        $data['fee_master']->paid_amount = $data['student']->feeCollect()->whereIn('fee_masters_id',$request->chkIds)->sum('paid_amount');
        $data['fee_master']->balance =
            ($data['fee_master']->fee_amount - ($data['fee_master']->paid_amount + $data['fee_master']->discount))+ $data['fee_master']->fine;

        return view(parent::loadDataToView('print.student-fee.feemaster-student-ledger'), compact('data'));
    }

    public function printCollection(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        $data['fee_collection'] = FeeCollection::find($id);

        $data['student'] = $data['fee_collection']->students()->select('students.id','students.reg_no','students.university_reg', 'students.first_name','students.middle_name','students.last_name',
                'students.faculty','students.batch','students.semester','pd.father_first_name','pd.father_middle_name','pd.father_last_name','sb.title')
                ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
                ->join('student_batches as sb','sb.id','=', 'students.batch')
                ->first();

        return view(parent::loadDataToView('print.student-fee.collection'), compact('data'));
    }

    public function todayReceiptAmount(Request $request, $id)
    {
        $id = decrypt($id);
        $today = Carbon::parse(today())->format('Y-m-d');
        $data['student'] = Student::select('students.id','students.reg_no','students.university_reg', 'students.first_name','students.middle_name','students.last_name',
            'students.faculty','students.batch','students.semester','pd.father_first_name','pd.father_middle_name','pd.father_last_name','sb.title')
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('student_batches as sb','sb.id','=', 'students.batch')
            ->find($id);

        /*total Calculation on Table Foot*/
        $fee_amount = $data['student']->feeMaster()->sum('fee_amount');
        $discount = $data['student']->feeCollect()->sum('discount');
        $fine = $data['student']->feeCollect()->sum('fine');
        $paid_amount = $data['student']->feeCollect()->sum('paid_amount');
        $balance = ($fee_amount - ($paid_amount + $discount))+ $fine;
        $data['total_due'] = $balance;

        $data['student']->paid_amount = $data['student']->feeCollect()->where([['date','=',$today],['students_id','=',$id]])->sum('paid_amount');

        return view(parent::loadDataToView('print.student-fee.today-receipt'), compact('data'));

    }

    public function todayReceiptDetail(Request $request, $id)
    {
        $id = decrypt($id);
        $today = Carbon::parse(today())->format('Y-m-d');
        $data['student'] = Student::select('students.id','students.reg_no','students.university_reg', 'students.first_name','students.middle_name','students.last_name',
            'students.faculty','students.batch','students.semester','pd.father_first_name','pd.father_middle_name','pd.father_last_name','sb.title')
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('student_batches as sb','sb.id','=', 'students.batch')
            ->find($id);

        $data['fee_collection'] = FeeCollection::where([['date','=',$today],['students_id','=',$id]])->where('status',1)->get();

        /*total Calculation on Table Foot*/
        $fee_amount = $data['student']->feeMaster()->sum('fee_amount');
        $discount = $data['student']->feeCollect()->sum('discount');
        $fine = $data['student']->feeCollect()->sum('fine');
        $paid_amount = $data['student']->feeCollect()->sum('paid_amount');
        $balance = ($fee_amount - ($paid_amount + $discount))+ $fine;
        $data['total_due'] = $balance;
        $data['semester'] = $data['student']->semester;

        return view(parent::loadDataToView('print.student-fee.today-receipt-detail'), compact('data'));

    }

    public function studentLedger(Request $request, $id)
    {
        $today = Carbon::today();
        $id = decrypt($id);
        $data['student'] = Student::select('students.id','students.reg_no','students.university_reg', 'students.first_name','students.middle_name','students.last_name',
            'students.faculty','students.batch','students.semester','pd.father_first_name','pd.father_middle_name','pd.father_last_name','sb.title')
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('student_batches as sb','sb.id','=', 'students.batch')
            ->find($id);

        $data['fee_master'] = $data['student']->feeMaster()->orderBy('fee_due_date','desc')->get();
        $data['fee_collection'] = $data['student']->feeCollect()->get();

        //$data['student']->payment_today = $data['student']->feeCollect()->where('date','=',Carbon::today())->sum('paid_amount');

        /*total Calculation on Table Foot*/
        $data['student']->fee_amount = $data['student']->feeMaster()->sum('fee_amount');
        $data['student']->discount = $data['student']->feeCollect()->sum('discount');
        $data['student']->fine = $data['student']->feeCollect()->sum('fine');
        $data['student']->paid_amount = $data['student']->feeCollect()->sum('paid_amount');
        $data['student']->balance =
            ($data['student']->fee_amount - ($data['student']->paid_amount + $data['student']->discount))+ $data['student']->fine;

        return view(parent::loadDataToView('print.student-fee.student-ledger'), compact('data'));

    }

    public function studentDue(Request $request, $id)
    {
        $id = decrypt($id);
        $data['student'] = Student::select('students.id','students.reg_no','students.university_reg', 'students.first_name','students.middle_name','students.last_name',
            'students.faculty','students.batch','students.semester','pd.father_first_name','pd.father_middle_name','pd.father_last_name','sb.title')
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('student_batches as sb','sb.id','=', 'students.batch')
            ->find($id);

        /*total Calculation on Table Foot*/
        $data['student']->fee_amount = $data['student']->feeMaster()->sum('fee_amount');
        $data['student']->discount = $data['student']->feeCollect()->sum('discount');
        $data['student']->fine = $data['student']->feeCollect()->sum('fine');
        $data['student']->paid_amount = $data['student']->feeCollect()->sum('paid_amount');
        $data['student']->balance = ($data['student']->fee_amount - ($data['student']->paid_amount + $data['student']->discount))+ $data['student']->fine;
        return view(parent::loadDataToView('print.student-fee.due-slip'), compact('data'));

    }

    public function studentDueDetail(Request $request, $id)
    {
        $id = decrypt($id);
        $data['student'] = Student::select('students.id','students.reg_no','students.university_reg', 'students.first_name','students.middle_name','students.last_name',
            'students.faculty','students.batch','students.semester','pd.father_first_name','pd.father_middle_name','pd.father_last_name','sb.title')
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('student_batches as sb','sb.id','=', 'students.batch')
            ->find($id);

        /*total Calculation on Table Foot*/
        $data['student']->fee_amount = $data['student']->feeMaster()->sum('fee_amount');
        $data['student']->discount = $data['student']->feeCollect()->sum('discount');
        $data['student']->fine = $data['student']->feeCollect()->sum('fine');
        $data['student']->paid_amount = $data['student']->feeCollect()->sum('paid_amount');
        $data['student']->balance = ($data['student']->fee_amount - ($data['student']->paid_amount + $data['student']->discount))+ $data['student']->fine;
        $Master = $data['student']->feeMaster()->get();
        //$Master = $data['student']->feeMaster()->where('fee_head','<>',3)->get();

        /*filter due using call back*/
        $filtered  = $Master->filter(function ($value, $key) {
            $feeMaster = $value->fee_amount;
            $feeCollection = $value->feeCollect->sum('paid_amount');
            $discount = $value->feeCollect->sum('discount');
            $fine = $value->feeCollect->sum('fine');
            $due = ($feeMaster + $fine) - ($discount + $feeCollection);
            if($due>0){
                $value->discount = $discount;
                $value->fine = $fine;
                $value->paid_amount = $feeCollection;
                $value->due = $due;
                return $value;
            }
        });

        $data['feemaster'] = $filtered;

        return view(parent::loadDataToView('print.student-fee.due-detail-slip'), compact('data'));

    }

    public function bulkDueDetailSlip(Request $request)
    {
        $studentId = $request->get('student');
        $students = Student::select('students.id','students.reg_no','students.university_reg', 'students.first_name','students.middle_name','students.last_name',
            'students.faculty','students.batch','students.semester','pd.father_first_name','pd.father_middle_name','pd.father_last_name','sb.title')
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('student_batches as sb','sb.id','=', 'students.batch')
            ->whereIn('students.id', $studentId)
            ->get();

        /*filter due using call back*/
        $filtered  = $students->filter(function ($student) {
            $student->master = $student->feeMaster()->get();
            //$student->master = $student->feeMaster()->where('fee_head','<>',3)->get();
            $student->master->filter(function ($value, $key) {
                $feeMaster = $value->fee_amount;
                $feeCollection = $value->feeCollect->sum('paid_amount');
                $discount = $value->feeCollect->sum('discount');
                $fine = $value->feeCollect->sum('fine');
                $due = ($feeMaster + $fine) - ($discount + $feeCollection);
                if($due>0){
                    $value->discount = $discount;
                    $value->fine = $fine;
                    $value->paid_amount = $feeCollection;
                    $value->due = $due;
                    return $value;
                }
            });

            /*Add Extra Field*/
            /*total Calculation on Table Foot*/
            $student->fee_amount = $student->feeMaster()->sum('fee_amount');
            $student->discount = $student->feeCollect()->sum('discount');
            $student->fine = $student->feeCollect()->sum('fine');
            $student->paid_amount = $student->feeCollect()->sum('paid_amount');
            $student->balance = ($student->fee_amount - ($student->paid_amount + $student->discount))+ $student->fine;
            $Master = $student->feeMaster()->get();
            /*return values*/
            return $student;
        });

        $data['student'] = $filtered;
        return view(parent::loadDataToView('print.student-fee.bulk-due-detail-slip'), compact('data'));

    }

    public function bulkDueSlip(Request $request)
    {
        $studentId = $request->get('student');
        $students = Student::select('students.id','students.reg_no','students.university_reg', 'students.first_name','students.middle_name','students.last_name',
            'students.faculty','students.batch','students.semester','pd.father_first_name','pd.father_middle_name','pd.father_last_name','sb.title')
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('student_batches as sb','sb.id','=', 'students.batch')
            ->whereIn('students.id', $studentId)
            ->get();

        /*filter due using call back*/
        $filtered  = $students->filter(function ($student) {
            $student->master = $student->feeMaster()->get();
            $student->master->filter(function ($value, $key) {
                $feeMaster = $value->fee_amount;
                $feeCollection = $value->feeCollect->sum('paid_amount');
                $discount = $value->feeCollect->sum('discount');
                $fine = $value->feeCollect->sum('fine');
                $due = ($feeMaster + $fine) - ($discount + $feeCollection);
                if($due>0){
                    $value->discount = $discount;
                    $value->fine = $fine;
                    $value->paid_amount = $feeCollection;
                    $value->due = $due;
                    return $value;
                }
            });

            /*Add Extra Field*/
            /*total Calculation on Table Foot*/
            $student->fee_amount = $student->feeMaster()->sum('fee_amount');
            $student->discount = $student->feeCollect()->sum('discount');
            $student->fine = $student->feeCollect()->sum('fine');
            $student->paid_amount = $student->feeCollect()->sum('paid_amount');
            $student->balance = ($student->fee_amount - ($student->paid_amount + $student->discount))+ $student->fine;
            $Master = $student->feeMaster()->get();
            /*return values*/
            return $student;
        });

        $data['student'] = $filtered;
        return view(parent::loadDataToView('print.student-fee.bulk-due-slip'), compact('data'));

    }

    public function feeVoucher(Request $request)
    {
        $month = $request->get('month');
        $studentId = $request->get('student');
        $students = Student::select('students.id','students.reg_no','students.university_reg', 'students.first_name','students.middle_name','students.last_name',
            'students.faculty','students.batch','students.semester','pd.father_first_name','pd.father_middle_name','pd.father_last_name','sb.title')
            ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
            ->join('student_batches as sb','sb.id','=', 'students.batch')
            ->whereIn('students.id', $studentId)
            ->get();

        /*filter due using call back*/
        $filtered  = $students->filter(function ($student) use($month) {
            $monthId = (int)$month;
            $student->master = FeeMaster::where('students_id',$student->id)->whereMonth('fee_due_date',$monthId)->get();
            // Keep only dues and reindex collection for consistent view access.
            $student->master = $student->master->filter(function ($value, $key) {
               // dd($value->created_at);
                $feeMaster = $value->fee_amount;
                $feeCollection = $value->feeCollect->sum('paid_amount');
                $discount = $value->feeCollect->sum('discount');
                $fine = $value->feeCollect->sum('fine');
                $due = ($feeMaster + $fine) - ($discount + $feeCollection);
                if($due>0){
                    //$value->discount = ;
                    $value->discount = $discount;
                    $value->fine = $fine;
                    $value->paid_amount = $feeCollection;
                    $value->due = $due;
                    return $value;
                }
            })->values();

            /*Add Extra Field*/
            /*total Calculation on Table Foot*/
            $student->fee_amount = $student->feeMaster()->sum('fee_amount');
            $student->discount = $student->feeCollect()->sum('discount');
            $student->fine = $student->feeCollect()->sum('fine');
            $student->paid_amount = $student->feeCollect()->sum('paid_amount');
            $student->balance = ($student->fee_amount - ($student->paid_amount + $student->discount))+ $student->fine;
            $Master = $student->feeMaster()->get();
            /*return values*/
            return $student;
        });

        $data['student'] = $filtered;
        return view(parent::loadDataToView('print.student-fee.generate-fee-voucher'), compact('data'));

    }


    public function dateReceiptAmount(Request $request,$id, $date)
    {
        $id = decrypt($id);
        $data['student'] = Student::select('id','reg_no', 'first_name','middle_name','last_name',
            'faculty','semester')
            ->find($id);

        /*total Calculation on Table Foot*/
        $fee_amount = $data['student']->feeMaster()->sum('fee_amount');
        $discount = $data['student']->feeCollect()->sum('discount');
        $fine = $data['student']->feeCollect()->sum('fine');
        $paid_amount = $data['student']->feeCollect()->sum('paid_amount');
        $balance = ($fee_amount - ($paid_amount + $discount))+ $fine;
        $data['total_due'] = $balance;

        $data['student']->paid_amount = $data['student']->feeCollect()->where([['date','=',$date],['students_id','=',$id]])->sum('paid_amount');
        $data['student']->paid_date = $date;
        //dd($data['student']->toArray());

        return view(parent::loadDataToView('print.student-fee.date-receipt'), compact('data'));

    }

    public function dateReceiptDetail(Request $request, $id, $date)
    {
        $id = decrypt($id);
        $data['student'] = Student::select('id','reg_no', 'first_name','middle_name','last_name',
            'faculty','semester')
            ->find($id);

        $data['fee_collection'] = FeeCollection::where([['date','=',$date],['students_id','=',$id]])->get();
        //$data['fee_collection'] = $data['student']->feeCollect()->where('date','=',$date)->get();

        /*total Calculation on Table Foot*/
        $fee_amount = $data['student']->feeMaster()->sum('fee_amount');
        $discount = $data['student']->feeCollect()->sum('discount');
        $fine = $data['student']->feeCollect()->sum('fine');
        $paid_amount = $data['student']->feeCollect()->sum('paid_amount');
        $balance = ($fee_amount - ($paid_amount + $discount))+ $fine;
        $data['total_due'] = $balance;

        return view(parent::loadDataToView('print.student-fee.date-receipt-detail'), compact('data'));

    }


    public function printOnlinePaymentReceipt($id)
    {
        try {
            $id = decrypt($id);;
            $data = [];
            // Find the payment record
            $data['payment'] = OnlinePayment::findOrFail($id);            
            // Get student details with relations
            $data['student'] = Student::findOrFail($data['payment']->students_id);
            return view(parent::loadDataToView('print.student-fee.online-payment-receipt'), compact('data'));

        } catch (\Exception $e) {
            abort(404, 'Receipt not found');
        }
    }

}

