<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\Account\Report;

use App\Http\Controllers\CollegeBaseController;
use App\Models\BankTransaction;
use App\Models\FeeCollection;
use App\Models\SalaryPay;
use App\Models\Student;
use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use URL;
class FeeCollectionReportController extends CollegeBaseController
{
    protected $base_route = 'account.report.fee-collection';
    protected $view_path = 'account.report.fee-collection';
    protected $panel = 'Fee Collection Report';
    protected $filter_query = [];

    public function __construct()
    {


    }

    public function feeCollection(Request $request)
    {
        $data = [];
        $date = Carbon::now()->toDateString();
        if($request->all()){
            if ($request->start_date && $request->end_date) {
                $collection = FeeCollection::where(function ($query) use ($request) {
                    /* fee_collections.date carries a clock time, so the last day of a range has to be
                       closed at 23:59:59 or it is dropped entirely - and a start date on its own was
                       compared with '=', matching midnight and nothing else. */
                    $this->filterDayRange($query, 'date',
                        $request->get('start_date'), $request->get('end_date'));

                    if ($request->filled('start_date')) {
                        $this->filter_query['start_date'] = $request->get('start_date');
                    }
                    if ($request->filled('end_date')) {
                        $this->filter_query['end_date'] = $request->get('end_date');
                    }

                    if($request->has('payment_method')){
                        $query->where('payment_method', '=', $request->get('payment_method'));
                        $this->filter_query['payment_method'] = $request->get('payment_method');
                    }
                })
                ->get();

                $studentsId = $collection->pluck('students_id')->toArray();
                //dd(array_unique($studentsId));
                $students = Student::select('students.id','students.reg_no','students.reg_date', 'students.first_name',
                    'students.middle_name', 'students.last_name','students.faculty','students.semester','ai.mobile_1', 'pd.father_first_name', 'pd.father_middle_name',
                    'pd.father_last_name','students.academic_status','students.status')
                    ->whereIn('students.id',array_unique($studentsId))
                    ->where(function ($query) use ($request) {
                        $this->commonStudentFilterCondition($query, $request);
                    })
                    ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
                    ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                    ->get();

                if($students){
                    $filtered  = $students->filter(function ($student) use($request) {
                        $student->paids = $student->feeCollect()
                                                    ->select('fee_collections.students_id', 'fee_collections.fee_masters_id', 'fee_collections.date',
                                                        'fee_collections.discount', 'fee_collections.fine', 'fee_collections.paid_amount',
                                                        'fee_collections.payment_method','fee_collections.note','fee_collections.response','fee_collections.status','fm.semester','fm.fee_head')
                                                    ->where(function ($query) use ($request) {
                                                        /* fee_collections.date carries a clock time, so the last day of a range has to be
                                                           closed at 23:59:59 or it is dropped entirely - and a start date on its own was
                                                           compared with '=', matching midnight and nothing else. */
                                                        $this->filterDayRange($query, 'date',
                                                            $request->get('start_date'), $request->get('end_date'));

                                                        if ($request->filled('start_date')) {
                                                            $this->filter_query['start_date'] = $request->get('start_date');
                                                        }
                                                        if ($request->filled('end_date')) {
                                                            $this->filter_query['end_date'] = $request->get('end_date');
                                                        }

                                                        if($request->has('payment_method')){
                                                            $query->where('payment_method', '=', $request->get('payment_method'));
                                                            $this->filter_query['payment_method'] = $request->get('payment_method');
                                                        }
                                                    })
                                                    ->join('fee_masters as fm','fm.id','=','fee_collections.fee_masters_id')
                                                    ->orderBy('date')
                                                    ->get();
                        $student->total_paid_amount = $student->paids->sum('paid_amount');
                        $student->total_discount = $student->paids->sum('discount');
                        $student->total_fine = $student->paids->sum('fine');

                        //dd($student->paids->toArray());
                        return $student;
                    });
                }

                $data['student'] =$filtered;
                $data['print_head'] = $this->panel.' - '.Carbon::parse($request->start_date)->format('m/d/Y').' To '.Carbon::parse($request->end_date)->format('m/d/Y');
                $data['tag'] = 'daily';
            }
            else{
                $date = Carbon::today()->format('Y-m-d');
                /* Only receipts in force decide who appears in the list; a student whose
                   only receipt was cancelled has not paid. */
                $collection = FeeCollection::where('status', 1)->get();
                $studentsId = $collection->pluck('students_id');
                $students = Student::select('students.id','students.reg_no','students.reg_date', 'students.first_name',
                    'students.middle_name', 'students.last_name','students.faculty','students.semester','ai.mobile_1', 'pd.father_first_name', 'pd.father_middle_name',
                    'pd.father_last_name','students.academic_status','students.status')
                    ->whereIn('students.id',$studentsId)
                    ->where(function ($query) use ($request) {
                        $this->commonStudentFilterCondition($query, $request);
                    })
                    ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
                    ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                    ->get();

                if($students){
                    $filtered  = $students->filter(function ($student) use($date) {
                        $student->date = $date;
                        /* Filtered on the day the money was taken, not on the day the row
                           happened to be written. They are normally the same, but a receipt
                           re-issued later - as the admission split does - carries today's
                           created_at while belonging to a payment made weeks ago, and the
                           report then showed a whole term's admission money as today's
                           collection. */
                        $collectionFees = $student->feeCollect()->whereDate('date',$date)->get();
                        $student->paid_amount = $collectionFees->sum('paid_amount');
                        $student->discount = $collectionFees->sum('discount');
                        $student->fine = $collectionFees->sum('fine');
                        if($student->paid_amount > 0){
                            return $student;
                        }
                    });
                }

                $data['student'] =$filtered;

                $data['print_head'] = $this->panel.' - '.Carbon::parse($date)->format('m/d/Y');
                $feeCollection = $this->dateFeeCollection($date);
                $data['fee_collection'] = $feeCollection->groupBy('fee_head');
                $data['fee_collection_total'] = $feeCollection->sum('paid_amount');
                $data['tag'] = 'today';
            }

        }else{

            $date = Carbon::today()->format('Y-m-d');
            /* Only receipts in force decide who appears in the list; a student whose only receipt
               was cancelled has not paid. */
            $collection = FeeCollection::where('status', 1)->get();
            $studentsId = $collection->pluck('students_id');
            $students = Student::select('students.id','students.reg_no','students.reg_date', 'students.first_name',
                'students.middle_name', 'students.last_name','students.faculty','students.semester','ai.mobile_1', 'pd.father_first_name', 'pd.father_middle_name',
                'pd.father_last_name','students.academic_status','students.status')
                ->whereIn('students.id',$studentsId)
                ->where(function ($query) use ($request) {
                    $this->commonStudentFilterCondition($query, $request);
                })
                ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
                ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                ->get();

            if($students){
                $filtered  = $students->filter(function ($student) use($date) {
                    $student->date = $date;
                    $student->paid_amount = $student->feeCollect()->whereDate('date',$date)->sum('paid_amount');
                    $student->discount = $student->feeCollect()->whereDate('date',$date)->sum('discount');
                    $student->fine = $student->feeCollect()->whereDate('date',$date)->sum('fine');
                    if($student->paid_amount > 0){
                        return $student;
                    }
                });
            }

            $data['student'] =$filtered;

            $data['print_head'] = $this->panel.' - '.Carbon::parse($date)->format('m/d/Y');
            $feeCollection = $this->dateFeeCollection($date);
            $data['fee_collection'] = $feeCollection->groupBy('fee_head');
            $data['fee_collection_total'] = $feeCollection->sum('paid_amount');
            $data['tag'] = 'today';

        }

        $data['faculties'] = $this->activeFaculties();
        $data['batch'] = $this->activeBatch();
        $data['academic_status'] = $this->activeStudentAcademicStatus();

        $method = FeeCollection::pluck('payment_method','payment_method')->unique()->toArray();
        $methods = array_prepend($method,'','');
        $data['payment_method'] = $methods;

        $data['fee_heads'] = $this->activeFeeHead();

        $data['filter_query'] = $this->filter_query;
        $data['url'] = URL::current();

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function dateFeeCollection($date)
    {
        $feeCollection = FeeCollection::select('fee_collections.students_id', 'fee_collections.fee_masters_id',
            'fee_collections.date', 'fee_collections.discount', 'fee_collections.fine', 'fee_collections.paid_amount',
            'fee_collections.payment_method','fee_collections.note','fee_collections.created_by','fee_collections.status as fc_status',
            'fm.status as fm_status','fm.fee_head')
            ->whereDate('fee_collections.date', '=', $date)
            /* Receipts in force only. A cancelled receipt is kept so the history survives, not
               so it can be counted again - without this the report reads double. */
            ->where('fee_collections.status', 1)
            ->join('fee_masters as fm','fm.id','=','fee_collections.fee_masters_id')
            ->orderBy('fee_collections.created_at','desc')
            ->get();

        return $feeCollection;
    }

}
