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
use App\Models\Faculty;
use App\Models\FeeCollection;
use App\Models\OnlinePayment;
use App\Models\Semester;
use App\Models\SalaryPay;
use App\Models\Student;
use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use URL;
class OnlineFeePaymentReportController extends CollegeBaseController
{
    protected $base_route = 'account.report.online-payment';
    protected $view_path = 'account.report.online-payment';
    protected $panel = 'Online Fee Payment Report';
    protected $filter_query = [];

    public function __construct()
    {


    }

    public function onlinePayments(Request $request)
    {
        $data = [];

        /* Which status this sheet is a report of.
           Left open, the report added unverified payments into the same Total as real receipts -
           which is exactly why the sheet and the Fees Dashboard disagreed, by the unverified
           amount and nothing else. Verified is what this report means by money, so that is the
           default; the Status filter still reaches the rest.
           filled() rather than has(), or an empty "Select Status" would read as 'not-verified'
           and quietly show only the pending ones. */
        $statusWanted = 1;
        if ($request->filled('verify_status')) {
            $statusWanted = $request->get('verify_status') == 'verify' ? 1 : 0;
            $this->filter_query['op.status'] = $request->get('verify_status');
        }

        /* Everything except the status, kept in one place so the same filters can be asked twice:
           once for the rows, and once to count what the status is holding back. */
        $applyFilters = function ($query) use ($request) {
            $this->commonStudentFilterCondition($query, $request);

            if ($request->has('pay_date_start') && $request->has('pay_date_end')) {
                $query->whereBetween('op.date', [$request->get('pay_date_start'), $request->get('pay_date_end')]);
                $this->filter_query['op.pay_date_start'] = $request->get('pay_date_start');
                $this->filter_query['op.pay_date_end'] = $request->get('pay_date_end');
            } elseif ($request->has('pay_date_start')) {
                $query->where('op.date', '=', $request->get('pay_date_start'));
                $this->filter_query['op.pay_date_start'] = $request->get('pay_date_start');
            } elseif ($request->has('op.pay_date_end')) {
                $query->where('op.date', '=', $request->get('pay_date_end'));
                $this->filter_query['op.pay_date_end'] = $request->get('pay_date_end');
            }

            if ($request->has('payment_gateway')) {
                $query->where('op.payment_gateway', '=', $request->payment_gateway);
                $this->filter_query['op.payment_gateway'] = $request->payment_gateway;
            }
        };

        $selection = ['students.id','students.reg_no','students.first_name',
            'students.middle_name', 'students.last_name','students.faculty','students.semester',
            'op.id as payment_id','op.date', 'op.amount', 'op.payment_gateway', 'op.ref_no', 'op.ref_text',
            'op.status as payment_status','op.created_by as paid_by'];

        $rows = Student::select($selection)
            ->join('online_payments as op', 'op.students_id', '=', 'students.id')
            ->where('op.status', $statusWanted);

        if ($request->all()) {
            $rows->where($applyFilters);
        }

        $data['student'] = $rows->get();

        /* What this status is keeping off the sheet.
           Without it the Not Verified card would read zero for ever, and a payment stuck at the
           gateway would never be noticed by anyone reading this report - the money would simply
           be missing and nothing would say so. Only asked when the reader has not gone looking
           for the unverified ones themselves. */
        $data['op_withheld'] = null;
        if ($statusWanted === 1) {
            $withheld = Student::select('op.amount')
                ->join('online_payments as op', 'op.students_id', '=', 'students.id')
                ->where('op.status', 0);

            if ($request->all()) {
                $withheld->where($applyFilters);
            }

            $withheld = $withheld->get();
            if ($withheld->count()) {
                $data['op_withheld'] = ['count' => $withheld->count(), 'sum' => $withheld->sum('amount')];
            }
        }


       /* $filteredStudent  = $students->filter(function ($student) {
            $student->fee_amount = $student->feeMaster()->sum('fee_amount');
            $student->paid_amount = $student->feeCollect()->sum('paid_amount');
            $student->discount = $student->feeCollect()->sum('discount');
            $student->fine = $student->feeCollect()->sum('fine');
            $student->balance = ($student->fee_amount + $student->fine) - ($student->discount + $student->paid_amount);
            if($student->balance > 0){
                return $student;
            }
        });

        $data['student'] = $filteredStudent;*/


        /* What the sheet is a report of. The filter boxes do not print, so without this a
           printed page of eighty names does not say whose department's money it holds. */
        $data['op_department'] = $request->get('faculty') > 0
            ? $this->getFacultyTitle($request->get('faculty'))
            : 'All Departments';
        $data['op_meta'] = $this->onlinePaymentHeading($request);
        $data['print_head'] = $data['op_department'];

        /* Department and semester names resolved once for the whole list. Looked up row by row -
           which is what this report did - each one costs a query, so eighty payments meant a
           hundred and sixty of them to print two columns. */
        $data['faculty_titles'] = Faculty::whereIn('id', $data['student']->pluck('faculty')->filter()->unique()->all())
            ->pluck('faculty', 'id')->all();
        $data['semester_titles'] = Semester::whereIn('id', $data['student']->pluck('semester')->filter()->unique()->all())
            ->pluck('semester', 'id')->all();

        $data['faculties'] = $this->activeFaculties();
        $data['batch'] = $this->activeBatch();
        $data['academic_status'] = $this->activeStudentAcademicStatus();

        $gateway = OnlinePayment::get()->pluck('payment_gateway','payment_gateway')->toArray();
        $data['payment_gateway'] = array_prepend($gateway,'Select Gateway','');

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    /**
     * The lines that sit under the report title on the printed sheet.
     *
     * Only what was actually asked for is listed. A row reading "Gateway: All" tells the reader
     * nothing and pushes the useful lines further apart.
     */
    private function onlinePaymentHeading(Request $request)
    {
        $meta = [];

        if ($request->get('semester_select') > 0) {
            $meta[] = ['label' => 'Sem./Section', 'value' => $this->getSemesterTitle($request->get('semester_select'))];
        }

        if ($request->get('batch') > 0) {
            $meta[] = ['label' => 'Batch', 'value' => $this->getStudentBatchById($request->get('batch'))];
        }

        $from = $request->get('pay_date_start');
        $to = $request->get('pay_date_end');
        if ($from && $to) {
            /* A real dash rather than the HTML entity: the blade escapes these values, because
               the gateway and the dates arrive from the query string. */
            $meta[] = ['label' => 'Payment Date', 'value' => Carbon::parse($from)->format('d M Y').' to '.Carbon::parse($to)->format('d M Y')];
        } elseif ($from) {
            $meta[] = ['label' => 'Payment Date', 'value' => Carbon::parse($from)->format('d M Y')];
        } elseif ($to) {
            $meta[] = ['label' => 'Payment Date', 'value' => 'Up to '.Carbon::parse($to)->format('d M Y')];
        }

        if ($request->get('payment_gateway')) {
            $meta[] = ['label' => 'Gateway', 'value' => $request->get('payment_gateway')];
        }

        /* Always stated, never left off. A sheet that does not say which status it holds reads
           like a complete record of every payment, and it is not one either way. */
        if ($request->filled('verify_status')) {
            $meta[] = ['label' => 'Status', 'value' => $request->get('verify_status') == 'verify' ? 'Verified' : 'Not Verified'];
        } else {
            $meta[] = ['label' => 'Status', 'value' => 'Verified only'];
        }

        return $meta;
    }

    public function feeCollection(Request $request)
    {
        $data = [];
        $date = Carbon::now()->toDateString();
        if($request->all()){
            if ($request->start_date && $request->end_date) {
                $collection = FeeCollection::where(function ($query) use ($request) {
                    if ($request->has('start_date') && $request->has('end_date')) {
                        $query->whereBetween('date', [$request->get('start_date'), $request->get('end_date')]);
                        $this->filter_query['start_date'] = $request->get('start_date');
                        $this->filter_query['end_date'] = $request->get('end_date');
                    } elseif ($request->has('start_date')) {
                        $query->where('date', '=', $request->get('start_date'));
                        $this->filter_query['start_date'] = $request->get('start_date');
                    } elseif ($request->has('end_date')) {
                        $query->where('date', '=', $request->get('end_date'));
                        $this->filter_query['end_date'] = $request->get('end_date');
                    }

                    if($request->has('payment_method')){
                        $query->where('payment_method', '=', $request->get('payment_method'));
                        $this->filter_query['payment_method'] = $request->get('payment_method');
                    }
                })
                ->get();

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
                    $filtered  = $students->filter(function ($student) use($request) {
                        $student->paids = $student->feeCollect()
                                                    ->where(function ($query) use ($request) {
                                                        if ($request->has('start_date') && $request->has('end_date')) {
                                                            $query->whereBetween('date', [$request->get('start_date'), $request->get('end_date')]);
                                                            $this->filter_query['start_date'] = $request->get('start_date');
                                                            $this->filter_query['end_date'] = $request->get('end_date');
                                                        } elseif ($request->has('start_date')) {
                                                            $query->where('date', '=', $request->get('start_date'));
                                                            $this->filter_query['start_date'] = $request->get('start_date');
                                                        } elseif ($request->has('end_date')) {
                                                            $query->where('date', '=', $request->get('end_date'));
                                                            $this->filter_query['end_date'] = $request->get('end_date');
                                                        }

                                                        if($request->has('payment_method')){
                                                            $query->where('payment_method', '=', $request->get('payment_method'));
                                                            $this->filter_query['payment_method'] = $request->get('payment_method');
                                                        }
                                                    })
                                                    ->get();
                        $student->total_paid_amount = $student->paids->sum('paid_amount');
                        $student->total_discount = $student->paids->sum('discount');
                        $student->total_fine = $student->paids->sum('fine');
                        return $student;
                    });
                }

                $data['student'] =$filtered;
                $data['print_head'] = $this->panel.' - '.Carbon::parse($request->start_date)->format('m/d/Y').' To '.Carbon::parse($request->end_date)->format('m/d/Y');
                $data['tag'] = 'daily';
            }
            else{
                $date = Carbon::today()->format('Y-m-d');
                $collection = FeeCollection::get();
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
                        $student->paid_amount = $student->feeCollect()->where('date',$date)->sum('paid_amount');
                        $student->discount = $student->feeCollect()->where('date',$date)->sum('discount');
                        $student->fine = $student->feeCollect()->where('date',$date)->sum('fine');
                        return $student;
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
            $collection = FeeCollection::get();
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
                    $student->paid_amount = $student->feeCollect()->where('date',$date)->sum('paid_amount');
                    $student->discount = $student->feeCollect()->where('date',$date)->sum('discount');
                    $student->fine = $student->feeCollect()->where('date',$date)->sum('fine');
                    return $student;
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
            ->join('fee_masters as fm','fm.id','=','fee_collections.fee_masters_id')
            ->orderBy('fee_collections.created_at','desc')
            ->get();

        return $feeCollection;
    }


}
