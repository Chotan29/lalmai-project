<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\Account\Fees;

use App\Http\Controllers\CollegeBaseController;
use App\Models\Addressinfo;
use App\Models\Faculty;
use App\Models\FeeCollection;
use App\Models\FeeHead;
use App\Models\FeeMaster;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use URL;
use ViewHelper;
class FeesBaseController extends CollegeBaseController
{
    protected $base_route = 'account.fees';
    protected $view_path = 'account.fees';
    protected $panel = 'Fees Collection';
    protected $filter_query = [];

    public function __construct()
    {

    }

    /**
     * What the reader is looking at, in words, for the top of the printed sheet.
     *
     * A report that does not say which dates and which head it covers is a page of numbers
     * somebody has to take on trust. Read back off the request rather than off the rows, so an
     * empty result still says what was asked for.
     */
    private function feeCollectionFilterSummary(Request $request)
    {
        $parts = [];

        $from = trim((string) $request->get('fee_collection_date_start'));
        $to = trim((string) $request->get('fee_collection_date_end'));

        if ($from !== '' && $to !== '') {
            $parts[] = 'Date: ' . $from . ' to ' . $to;
        } elseif ($from !== '') {
            $parts[] = 'Date: from ' . $from;
        } elseif ($to !== '') {
            $parts[] = 'Date: up to ' . $to;
        }

        if ($request->get('fee_heads') > 0) {
            $parts[] = 'Fee Head: ' . ViewHelper::getFeeHeadById($request->get('fee_heads'));
        }

        if (trim((string) $request->get('payment_method')) !== '') {
            $parts[] = 'Method: ' . $request->get('payment_method');
        }

        if ($request->get('faculty') > 0) {
            $parts[] = 'Department: ' . ViewHelper::getFacultyTitle($request->get('faculty'));
        }

        if ($request->get('semester_select') > 0) {
            $parts[] = 'Class: ' . ViewHelper::getSemesterTitle($request->get('semester_select'));
        }

        if (trim((string) $request->get('reg_no')) !== '') {
            $parts[] = 'Reg. No: ' . $request->get('reg_no');
        }

        return $parts ? implode('   |   ', $parts) : 'All receipts, no filter applied';
    }

    /**
     * The receive-history query, written once.
     *
     * Three things read this list - the page on screen, the total under it, and the printed
     * report - and they must never be able to disagree about what "the filter" means. Building
     * the query in one place is what guarantees that: a filter added here is a filter the
     * printed sheet obeys, with nothing to remember.
     */
    private function feeCollectionQuery(Request $request, $ordered = true)
    {
        $query = FeeCollection::select('fee_collections.created_at','fee_collections.students_id', 'fee_collections.fee_masters_id',
            'fee_collections.date', 'fee_collections.discount', 'fee_collections.fine', 'fee_collections.paid_amount',
            'fee_collections.payment_method','fee_collections.note','fee_collections.ref_no','fee_collections.external_ref_no','fee_collections.created_by','fee_collections.status as fc_status','fee_collections.verified_at',
            'fm.status as fm_status','fm.fee_head',
            'students.reg_no','students.reg_date', 'students.first_name','students.middle_name', 'students.last_name','students.semester');

        if ($request->all()) {
            $query->where(function ($query) use ($request) {

                $this->commonStudentFilterCondition($query, $request);

                /* fee_collections.date is a datetime and every one of the receipts on file
                   carries a real clock time, so a range ending on the 10th used to end at
                   midnight on the 10th and leave that whole day's money off the report - and
                   off the total under it. Closed at 23:59:59 through the shared helper. */
                $this->filterDayRange($query, 'fee_collections.date',
                    $request->get('fee_collection_date_start'),
                    $request->get('fee_collection_date_end'));

                if ($request->filled('fee_collection_date_start')) {
                    $this->filter_query['fee_collection_date_start'] = $request->get('fee_collection_date_start');
                }
                if ($request->filled('fee_collection_date_end')) {
                    $this->filter_query['fee_collection_date_end'] = $request->get('fee_collection_date_end');
                }

                if ($request->has('fee_heads') && $request->get('fee_heads') > 0) {
                    $query->where('fm.fee_head', '=',$request->fee_heads);
                    $this->filter_query['fm.fee_head'] = $request->fee_heads;
                }

                if ($request->has('payment_method') && $request->get('payment_method') !=null) {
                    $query->where('fee_collections.payment_method', 'like', '%' . $request->payment_method . '%');
                    $this->filter_query['fee_collections.payment_method'] = $request->payment_method;
                }

            });
        }

        $query->join('students', 'students.id','=','fee_collections.students_id')
            ->join('fee_masters as fm','fm.id','=','fee_collections.fee_masters_id');

        /*The totals ask for one row of sums, and an ORDER BY on a column that is not being
          grouped is both pointless there and something a strict MySQL will refuse outright.*/
        if ($ordered) {
            $query->orderBy('fee_collections.created_at','desc');
        }

        return $query;
    }

    /**
     * What the whole filter comes to - not what one page of it comes to.
     *
     * The total under the table used to add up the rows it could see, so a filter matching three
     * hundred receipts showed the sum of the twenty-five on screen and looked like a real answer.
     * Counted in the database over the same query the list is drawn from.
     */
    private function feeCollectionTotals(Request $request)
    {
        $row = $this->feeCollectionQuery($request, false)
            ->selectRaw('COUNT(*) as row_count, COALESCE(SUM(fee_collections.paid_amount),0) as paid_amount, COALESCE(SUM(fee_collections.fine),0) as fine, COALESCE(SUM(fee_collections.discount),0) as discount')
            ->first();

        return [
            'row_count' => (int) ($row->row_count ?? 0),
            'paid_amount' => (float) ($row->paid_amount ?? 0),
            'fine' => (float) ($row->fine ?? 0),
            'discount' => (float) ($row->discount ?? 0),
        ];
    }

    public function index(Request $request)
    {
        $data = [];

        /*The office prints this list as a report. On screen it stays paged - a clerk wants the
          latest receipts, not three hundred rows - but a printed sheet that stopped after
          twenty-five would be a sheet that quietly left money out, so printing takes the lot.*/
        $printMode = (bool) $request->get('print');

        $query = $this->feeCollectionQuery($request);

        $data['feesCollection'] = $printMode
            ? $query->get()
            : $query->paginate(env('PAGINATION_LIMIT',$this->pagination_limit));

        $data['totals'] = $this->feeCollectionTotals($request);
        $data['print_mode'] = $printMode;
        $data['filter_summary'] = $this->feeCollectionFilterSummary($request);

        $data['faculties'] = $this->activeFaculties();
        $data['batch'] = $this->activeBatch();
        $data['academic_status'] = $this->activeStudentAcademicStatus();
        $data['payment_method'] = $this->activePaymentMethod();
        $data['fee_heads'] = $this->activeFeeHead();

        $data['village'] = $this->addressVillage();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        /*The printed sheet is its own view. Keeping it apart means the screen the office uses
          every day is not carrying print rules it has to work around, and a change to one
          cannot quietly break the other.*/
        $view = $printMode ? '.print' : '.index';

        return view(parent::loadDataToView($this->view_path.$view), compact('data'));
    }

    public function balance(Request $request)
    {
        $data = [];

        if($request->all()){
            $students = Student::select('students.id','students.reg_no','students.reg_date', 'students.first_name',
                'students.middle_name', 'students.last_name', 'students.student_image','students.status',
                'pd.father_first_name', 'pd.father_middle_name','pd.father_last_name','pd.father_mobile_1',
                'f.faculty','s.semester',
                'sgd.guardian_first_name','sgd.guardian_middle_name','sgd.guardian_last_name', 'sgd.guardian_mobile_1'
                )
                ->where(function ($query) use ($request) {
                    $this->commonStudentFilterCondition($query, $request);
                    if ($request->get('village') > 0) {
                        $query->where('ai.address', '=',  $request->village);
                        $this->filter_query['ai.address'] = $request->village;
                    }
                })
                ->join('parent_details as pd', 'pd.students_id', '=', 'students.id')
                ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                ->join('faculties as f', 'f.id', '=', 'students.faculty')
                ->join('semesters as s', 's.id', '=', 'students.semester')
                ->join('student_guardians as sg', 'sg.students_id', '=', 'students.id')
                ->join('guardian_details as sgd', 'sgd.id', '=', 'sg.id')
                ->get();

            if($request->due_status == 'overdue'){
                /*filter Over due using call back*/
                $filtered  = $students->filter(function ($student) {
                    $date = Carbon::today()->format('Y-m-d');
                    $feeMaster = $student->feeMaster()->select('id as fee_master_id','fee_amount')->where('fee_due_date','<=',$date)->get();
                    $collection = FeeCollection::select('fee_collections.id','fee_collections.students_id','fee_collections.fee_masters_id', 'fee_collections.date', 'fee_collections.discount', 'fee_collections.fine', 'fee_collections.paid_amount')
                        ->where('fm.fee_due_date','<=',$date)
                        ->where('fee_collections.students_id','=',$student->id)
                        ->join('fee_masters as fm','fm.id','=','fee_collections.fee_masters_id')
                        ->get();

                    $student->fee_amount = $feeMaster->sum('fee_amount');
                    $student->paid_amount = $collection->sum('paid_amount');
                    $student->discount = $collection->sum('discount');
                    $student->fine = $collection->sum('fine');
                    $student->balance = ($student->fee_amount + $student->fine) - ($student->discount + $student->paid_amount);
                    if($student->balance > 0){
                        return $student;
                    }
                });

            }else{
                /*filter due using call back*/
                $filtered  = $students->filter(function ($student) {
                    $student->fee_amount = $student->feeMaster()->sum('fee_amount');
                    $student->paid_amount = $student->feeCollect()->sum('paid_amount');
                    $student->discount = $student->feeCollect()->sum('discount');
                    $student->fine = $student->feeCollect()->sum('fine');
                    $student->balance = ($student->fee_amount + $student->fine) - ($student->discount + $student->paid_amount);
                    if($student->balance > 0){
                        return $student;
                    }
                });

            };

            $data['student'] = $filtered;
        }



        $data['faculties'] = $this->activeFaculties();
        $data['batch'] = $this->activeBatch();
        $data['academic_status'] = $this->activeStudentAcademicStatus();
        $data['fee_heads'] = $this->activeFeeHead();
        $data['village'] = $this->addressVillage();
        $data['months'] = $this->activeMonths();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.balance.index'), compact('data'));
    }

}
