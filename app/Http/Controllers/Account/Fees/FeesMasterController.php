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
use App\Models\BillingRun;
use App\Models\Faculty;
use App\Models\FeeHead;
use App\Models\FeeMaster;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use URL;
use ViewHelper;
class FeesMasterController extends CollegeBaseController
{
    protected $base_route = 'account.fees.master';
    protected $view_path = 'account.fees.master';
    protected $panel = 'Fees Master';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $data = [];
        if($request->all()) {
            $data['fee_master'] = FeeMaster::select('fee_masters.id', 'fee_masters.students_id', 'fee_masters.semester',
                'fee_masters.fee_head', 'fee_masters.fee_due_date', 'fee_masters.fee_due_date2', 'fee_masters.fee_due_date3', 'fee_masters.fee_amount', 'fee_masters.status',
                'students.reg_no', 'students.reg_date', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.semester')
                ->where(function ($query) use ($request) {

                    $this->commonStudentFilterCondition($query, $request);

                    if ($request->has('fee_due_date_start') && $request->has('fee_due_date_end')) {
                        $query->whereBetween('fee_masters.fee_due_date', [$request->get('fee_due_date_start'), $request->get('fee_due_date_end')]);
                        $this->filter_query['fee_due_date_start'] = $request->get('fee_due_date_start');
                        $this->filter_query['fee_due_date_end'] = $request->get('fee_due_date_end');
                    } elseif ($request->has('fee_due_date_start')) {
                        $query->where('fee_masters.fee_due_date', '>=', $request->get('fee_due_date_start'));
                        $this->filter_query['fee_due_date_start'] = $request->get('fee_due_date_start');
                    } elseif ($request->has('fee_due_date_end')) {
                        $query->where('fee_masters.fee_due_date', '<=', $request->get('fee_due_date_end'));
                        $this->filter_query['fee_due_date_end'] = $request->get('fee_due_date_end');
                    }

                    if ($request->has('fee_heads') && $request->get('fee_heads') > 0) {
                        $query->where('fee_masters.fee_head', '=', $request->fee_heads);
                        $this->filter_query['fee_head'] = $request->fee_heads;
                    }

                    if ($request->has('amount_start') && $request->has('amount_end')) {
                        $query->whereBetween('fee_masters.fee_amount', [$request->get('amount_start'), $request->get('amount_end')]);
                        $this->filter_query['amount_start'] = $request->get('amount_start');
                        $this->filter_query['amount_end'] = $request->get('amount_end');
                    } elseif ($request->has('amount_start')) {
                        $query->where('fee_masters.fee_amount', '>=', $request->get('amount_start'));
                        $this->filter_query['amount_start'] = $request->get('amount_start');
                    } elseif ($request->has('amount_end')) {
                        $query->where('fee_masters.fee_amount', '<=', $request->get('amount_end'));
                        $this->filter_query['amount_end'] = $request->get('amount_end');
                    }
                })
                ->orderBy('fee_masters.fee_due_date', 'desc')
                ->join('students', 'students.id', '=', 'fee_masters.students_id')
                ->paginate(env('PAGINATION_LIMIT',$this->pagination_limit));
        }else{
            $year = $this->getActiveYear();
            $data['fee_master'] = FeeMaster::select('fee_masters.id', 'fee_masters.students_id', 'fee_masters.semester',
                'fee_masters.fee_head', 'fee_masters.fee_due_date', 'fee_masters.fee_due_date2', 'fee_masters.fee_due_date3', 'fee_masters.fee_amount', 'fee_masters.status',
                'students.reg_no', 'students.reg_date', 'students.first_name', 'students.middle_name', 'students.last_name', 'students.semester')
                ->whereYear('fee_masters.fee_due_date', '=', $year)
                ->orderBy('fee_masters.fee_due_date', 'desc')
                ->join('students', 'students.id', '=', 'fee_masters.students_id')
                ->paginate(env('PAGINATION_LIMIT',$this->pagination_limit));
        }

        $data['faculties'] = $this->activeFaculties();
        $data['batch'] = $this->activeBatch();
        $data['academic_status'] = $this->activeStudentAcademicStatus();
        $data['fee_heads'] = $this->activeFeeHead();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function add(Request $request)
    {
        $data = [];
        if($request->all()) {
            if ($request->has('facility')) {
                /*with library facility*/
                if ($request->get('facility') == 1) {
                    $data['student'] = Student::select('students.id', 'students.reg_no', 'students.reg_date', 'students.first_name',
                        'students.middle_name', 'students.last_name', 'students.faculty', 'students.semester', 'students.academic_status', 'students.status')
                        ->where(function ($query) use ($request) {
                            $this->commonStudentFilterCondition($query, $request);
                        })
                        ->where('l.user_type','=',1)
                        ->join('library_members as l', 'l.member_id', '=', 'students.id')
                        ->get();
                }

                /*with Hostel facility*/
                if ($request->get('facility') == 2) {
                    $data['student'] = Student::select('students.id', 'students.reg_no', 'students.reg_date', 'students.first_name',
                        'students.middle_name', 'students.last_name', 'students.faculty', 'students.semester', 'students.academic_status', 'students.status')
                        ->where(function ($query) use ($request) {
                            $this->commonStudentFilterCondition($query, $request);
                        })
                        ->where('r.user_type',1)
                        ->join('residents as r', 'r.member_id', '=', 'students.id')
                        ->get();
                }

                /*with transport facility*/
                if ($request->get('facility') == 3) {
                    $data['student'] = Student::select('students.id', 'students.reg_no', 'students.reg_date', 'students.first_name',
                        'students.middle_name', 'students.last_name', 'students.faculty', 'students.semester', 'students.academic_status', 'students.status')
                        ->where(function ($query) use ($request) {
                            $this->commonStudentFilterCondition($query, $request);
                        })
                        ->where('tu.user_type',1)
                        ->join('transport_users as tu', 'tu.member_id', '=', 'students.id')
                        ->get();
                }

            } else {
                $data['student'] = Student::select('students.id', 'students.reg_no', 'students.reg_date', 'students.first_name',
                    'students.middle_name', 'students.last_name', 'students.faculty', 'students.semester', 'students.academic_status', 'students.status')
                    ->where(function ($query) use ($request) {
                        $this->commonStudentFilterCondition($query, $request);
                    })
                    ->get();
            }
        }

        $data['faculties'] = $this->activeFaculties();
        $data['batch'] = $this->activeBatch();
        $data['academic_status'] = $this->activeStudentAcademicStatus();
        $data['fee_heads'] = $this->activeFeeHead();

        $data['facility'] = ['0'=>'Select Facility','1'=>'Library','2'=>'Hostel','3'=>'Transport'];

        $feeHeadAll = FeeHead::Active()->orderby('fee_head_title')->get();
        $data['feeHead'] = $feeHeadAll->pluck('fee_head_title','id');

        $data['randId'] = $randomId = rand(999,1);
        //Create an array of option attribute
        $data['fee_head_attributes']  =  $feeHeadAll->mapWithKeys(function ($feeHead) use($randomId) {
            return [$feeHead->id => ['data-feeHead-amount' => $feeHead->fee_head_amount, 'data-rand-id' => $randomId]];
        })->all();

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }

    // public function store(Request $request)
    // {
    //     if ($request->has('chkIds')) {
    //         foreach ($request->get('chkIds') as $row_id) {
    //             $row = Student::find(decrypt($row_id));
    //             if ($row && $request->has('fee_head')) {
    //                 foreach ($request->get('fee_head') as $key => $fee_head) {
    //                     $date = Carbon::parse($request->get('fee_due_date')[$key])->format('Y-m-d');
    //                     $date2 = Carbon::parse($request->get('fee_due_date2')[$key])->format('Y-m-d');
    //                     $date3 = Carbon::parse($request->get('fee_due_date3')[$key])->format('Y-m-d');
    //                     FeeMaster::create([
    //                         'students_id' => $row->id,
    //                         'semester' => $row->semester,
    //                         'fee_head' => $request->get('fee_head')[$key],
    //                         'fee_due_date' => $date,
    //                         'fee_due_date2' => $date2,
    //                         'fee_due_date3' => $date3,
    //                         'fee_amount' => $request->get('fee_amount')[$key],
    //                         'created_by' => auth()->user()->id,
    //                     ]);
    //                 }
    //             }else {
    //                 $request->session()->flash($this->message_warning, 'Please, Add Fee Master at least one row.');
    //                 return redirect()->route($this->base_route);
    //             }
    //         }
    //     }else {
    //         $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
    //         return redirect()->route($this->base_route);
    //     }

    //     $request->session()->flash($this->message_success, $this->panel. ' Add Successfully.');
    //     return back();

    // }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'fee_due_date.*' => 'required|date|after_or_equal:today',
        'fee_head.*' => 'required',
        'fee_amount.*' => 'required|numeric',
    ], [
            'fee_due_date.*.required' => 'The due date is required',
            'fee_due_date.*.date' => 'The due date must be a valid date',
            'fee_due_date.*.after_or_equal' => 'The due date must be today or later',
        
        'fee_head.*.required' => 'Fee head is required',
        'fee_amount.*.required' => 'Amount is required',
        'fee_amount.*.numeric' => 'Amount must be a number',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    if ($request->has('chkIds')) {
        foreach ($request->get('chkIds') as $row_id) {
            $row = Student::find(decrypt($row_id));
            if ($row && $request->has('fee_head')) {
                foreach ($request->get('fee_head') as $key => $fee_head) {
                    // Process if validation passes
                    $date = Carbon::parse($request->get('fee_due_date')[$key])->format('Y-m-d');

                    FeeMaster::create([
                        'students_id' => $row->id,
                        'semester' => $row->semester,
                        'fee_head' => $request->get('fee_head')[$key],
                        'fee_due_date' => $date,
                        'fee_due_date2' => $date,
                        'fee_due_date3' => $date,
                        'fee_amount' => $request->get('fee_amount')[$key],
                        'created_by' => auth()->user()->id,
                    ]);
                }
            } else {
                $request->session()->flash($this->message_warning, 'Please, Add Fee Master at least one row.');
                return redirect()->route($this->base_route);
            }
        }
    } else {
        $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
        return redirect()->route($this->base_route);
    }

    $request->session()->flash($this->message_success, $this->panel. ' Add Successfully.');
    return back();
}

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        $data['row'] = FeeMaster::select('id', 'students_id', 'semester', 'fee_head','fee_due_date','fee_due_date2','fee_due_date3','fee_amount','status')
            ->where('id','=',$id)
            ->first();
        if (!$data['row'])
            return parent::invalidRequest();

        $data['row']->reg_no = parent::getStudentById($data['row']->students_id) ;
        $data['row']->student_name = parent::getStudentNameById($data['row']->students_id) ;
        $data['row']->semester = parent::getSemesterById($data['row']->semester) ;
        $data['row']->fee_head = parent::getFeeHeadById($data['row']->fee_head) ;

        $data['faculties'] = $this->activeFaculties();

        $data['url'] = URL::current();
        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.add'), compact('data'));
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = FeeMaster::find($id)) return parent::invalidRequest();
        $dueDate = Carbon::parse($request->get('fee_due_date'))->format('Y-m-d');
        $row->update([
            'fee_due_date' => $dueDate,
            'fee_due_date2' => $dueDate,
            'fee_due_date3' => $dueDate,
            'fee_amount' => $request->get('fee_amount'),
            'last_updated_by' => auth()->user()->id,
        ]);
        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return back();
        //return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = FeeMaster::find($id)) return parent::invalidRequest();

        // Block deletion if confirmed payments (status=1) exist
        $paidCount = $row->feeCollect()->count();
        if ($paidCount > 0) {
            $request->session()->flash(
                $this->message_warning,
                'Cannot delete: this fee has ' . $paidCount . ' confirmed payment record(s). '
                . 'Delete each payment entry first, then delete the fee.'
            );
            return redirect()->back();
        }

        // Remove any unpaid/cancelled collection records before deleting the master
        $row->collections()->where('status', '!=', 1)->delete();
        $row->delete();

        $request->session()->flash($this->message_success, $this->panel . ' Deleted Successfully.');
        return redirect()->back();
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {
                foreach ($request->get('chkIds') as $row_id) {
                    $row_id = decrypt($row_id);
                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            $row = FeeMaster::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }
                            break;
                        case 'delete':
                            $row = FeeMaster::find($row_id);
                            $row->delete();
                            break;
                    }
                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')) . ' Successfully.');
                else
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')).' successfully.');

                return redirect()->route($this->base_route);

            } else {
                $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
                return redirect()->route($this->base_route);
            }

        } else return parent::invalidRequest();

    }

    public function active(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = FeeHead::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->faculty.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = FeeHead::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->faculty.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function feeHtmlRow()
    {
        //get all head
        $feeHeadAll = FeeHead::Active()->orderby('fee_head_title')->get();
        $feeHead = $feeHeadAll->pluck('fee_head_title','id');
        //$feeHead = array_prepend($feeHead,'Select Fee Head','id');
        $randomId = rand(999,1);
        //Create an array of option attribute
        $fee_head_attributes =  $feeHeadAll->mapWithKeys(function ($feeHead) use($randomId) {
                return [$feeHead->id => ['data-feeHead-amount' => $feeHead->fee_head_amount, 'data-rand-id' => $randomId]];
            })->all();

        $response['html'] = view($this->view_path.'.includes.fee_tr', ['fee_heads' => $feeHead, "fee_head_attributes" => $fee_head_attributes, 'randId' => $randomId])->render();
        return response()->json(json_encode($response));
    }

    // -------------------------------------------------------
    // CLEAR FEES: Preview + bulk delete with payment protection
    // -------------------------------------------------------

    public function clearFees(Request $request)
    {
        $data['faculties']       = $this->activeFaculties();
        $data['batch']           = $this->activeBatch();
        $data['academic_status'] = $this->activeStudentAcademicStatus();
        $data['fee_heads']       = $this->activeFeeHead();
        $data['billing_runs']    = BillingRun::orderByDesc('run_date')
                                    ->whereIn('status', ['completed', 'partial', 'approved'])
                                    ->get(['id', 'period_label', 'period_key', 'run_date', 'status']);

        $data['preview']         = null;
        $data['filter_query']    = [];

        if ($request->isMethod('post') && $request->input('action') === 'preview') {
            $result = $this->_buildClearQuery($request);
            $query  = $result['query'];

            $fees = $query->with('collections')->get();

            $clearable  = [];
            $protected  = [];
            foreach ($fees as $fm) {
                $paid = $fm->collections()->where('status', 1)->sum('paid_amount');
                if ($paid > 0) {
                    $protected[] = $fm;
                } else {
                    $clearable[] = $fm;
                }
            }

            $data['preview'] = [
                'clearable_count'  => count($clearable),
                'protected_count'  => count($protected),
                'clearable_amount' => array_sum(array_column($clearable, 'fee_amount')),
            ];
            $data['filter_query'] = $result['filters'];
        }

        if ($request->isMethod('post') && $request->input('action') === 'execute') {
            $result = $this->_buildClearQuery($request);
            $query  = $result['query'];

            $fees = $query->with('collections')->get();

            $deleted   = 0;
            $protected = 0;
            $errors    = 0;

            DB::beginTransaction();
            try {
                foreach ($fees as $fm) {
                    $paid = $fm->collections()->where('status', 1)->sum('paid_amount');
                    if ($paid > 0) {
                        $protected++;
                        continue;
                    }
                    // Delete unpaid collections first, then master
                    $fm->collections()->where('status', '!=', 1)->delete();
                    $fm->delete();
                    $deleted++;
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                $errors++;
                $request->session()->flash($this->message_warning, 'Error during deletion: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }

            $msg = "{$deleted} fee(s) deleted successfully.";
            if ($protected > 0) $msg .= " {$protected} fee(s) were protected (have existing payments).";
            $request->session()->flash($this->message_success, $msg);
            return redirect()->route('account.fees.master.clear');
        }

        return view(parent::loadDataToView('account.fees.master.clear'), $data);
    }

    private function _buildClearQuery(Request $request)
    {
        $filters = [];

        $query = FeeMaster::join('students', 'students.id', '=', 'fee_masters.students_id')
                    ->select('fee_masters.*');

        // Student-level filter
        if ($request->filled('student_id')) {
            $sid = (int) $request->input('student_id');
            $query->where('fee_masters.students_id', $sid);
            $filters['student_id'] = $sid;
        }

        if ($request->filled('reg_no')) {
            $query->where('students.reg_no', 'like', '%' . $request->input('reg_no') . '%');
            $filters['reg_no'] = $request->input('reg_no');
        }

        // Class-level filters
        if ($request->filled('faculty') && $request->input('faculty') > 0) {
            $query->where('students.faculty', $request->input('faculty'));
            $filters['faculty'] = $request->input('faculty');
        }

        if ($request->filled('semester_select') && $request->input('semester_select') > 0) {
            $query->where('students.semester', $request->input('semester_select'));
            $filters['semester_select'] = $request->input('semester_select');
        }

        if ($request->filled('batch') && $request->input('batch') > 0) {
            $query->where('students.batch', $request->input('batch'));
            $filters['batch'] = $request->input('batch');
        }

        // Fee-specific filters
        if ($request->filled('fee_heads') && $request->input('fee_heads') > 0) {
            $query->where('fee_masters.fee_head', $request->input('fee_heads'));
            $filters['fee_heads'] = $request->input('fee_heads');
        }

        if ($request->filled('billing_run_id')) {
            $query->where('fee_masters.billing_run_id', $request->input('billing_run_id'));
            $filters['billing_run_id'] = $request->input('billing_run_id');
        }

        if ($request->filled('billing_period_key')) {
            $query->where('fee_masters.billing_period_key', 'like', '%' . $request->input('billing_period_key') . '%');
            $filters['billing_period_key'] = $request->input('billing_period_key');
        }

        if ($request->filled('fee_due_date_start')) {
            $query->where('fee_masters.fee_due_date', '>=', $request->input('fee_due_date_start'));
            $filters['fee_due_date_start'] = $request->input('fee_due_date_start');
        }

        if ($request->filled('fee_due_date_end')) {
            $query->where('fee_masters.fee_due_date', '<=', $request->input('fee_due_date_end'));
            $filters['fee_due_date_end'] = $request->input('fee_due_date_end');
        }

        // Status filter — default to unpaid only
        $statusFilter = $request->input('fee_status', 'unpaid');
        if ($statusFilter === 'unpaid') {
            $query->where('fee_masters.status', 'active');
            $filters['fee_status'] = 'unpaid';
        } elseif ($statusFilter === 'inactive') {
            $query->where('fee_masters.status', 'in-active');
            $filters['fee_status'] = 'inactive';
        }
        // 'all' = no status filter

        return ['query' => $query, 'filters' => $filters];
    }

}
