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

use App\Exports\FeeGroupDepartmentExport;
use App\Http\Controllers\CollegeBaseController;
use App\Models\BankTransaction;
use App\Models\FeeCollection;
use App\Models\FeeHeadGroup;
use App\Models\SalaryPay;
use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use URL;
class FeeCollectionHeadReportController extends CollegeBaseController
{
    protected $base_route = 'account.report.fee-collection-head';
    protected $view_path = 'account.report.fee-collection-head';
    protected $panel = 'Fee Head Collection Report';
    protected $filter_query = [];

    public function __construct()
    {


    }

    public function feeCollectionHead(Request $request)
    {
        $data = [];
        $date = Carbon::now()->toDateString();
        if($request->all()){
            /* A Main Fee Head is answered head by head, not date by date: the question being
               asked of it is "how much of this fee landed in each of its heads", and a date
               breakdown cannot show that. Checked before the date branches so the report type
               cannot change the answer. */
            $feeGroupId = $this->feeHeadGroupIdFromFilter($request->fee_heads);

            if($feeGroupId && $request->start_date && $request->end_date) {
                /* Title and period kept apart as well as joined: the printed sheet sets them on
                   separate lines, and splitting a formatted string back up in the view is how
                   headings end up mangled. print_head stays for anything already using it. */
                $data['fee_title'] = $this->feeFilterTitle($request->fee_heads);
                $data['fg_period'] = Carbon::parse($request->start_date)->format('d M Y')
                    . '  to  ' . Carbon::parse($request->end_date)->format('d M Y');
                $data['print_head'] = $data['fee_title'] . ' - [' . $data['fg_period'] . ']';
                $data['fee_group_rows'] = $this->feeGroupHeadBreakdown($feeGroupId, $request->start_date, $request->end_date);
                $data['fee_collection_total'] = $data['fee_group_rows']->sum('amount');
                $data['college_total'] = $data['fee_group_rows']->where('collected_by','!=','department')->sum('amount');
                $data['department_total'] = $data['fee_group_rows']->where('collected_by','department')->sum('amount');
                /* Whose money this is. A head-wise total answers "how much" but not "for how
                   many", and the two are only reconcilable together: a head divided by its rate
                   should land on the number of students, and where it does not the department
                   list is what says which. */
                $data['fee_group_departments'] = $this->feeGroupStudentsByDepartment(
                    $feeGroupId, $request->start_date, $request->end_date);
                $data['tag'] = 'fee_group';
                $data['fee_group_tag'] = 'fee_group';
                $data['url'] = URL::current();
                $data['row'] = collect($data);
            }
            elseif($request->fee_heads && $request->report_type && $request->start_date && $request->end_date) {
                if($request->report_type == 'daily') {
                    $period = CarbonPeriod::create($request->start_date, $request->end_date);
                    foreach ($period as $key => $date) {
                        $data['print_head'] = $this->feeFilterTitle($request->fee_heads).' - DAILY';
                        $data[$key]['table_head'] = Carbon::parse($date)->format('m-d-Y');
                        $feeCollection = $this->dateWithHeadFeeCollection($request->fee_heads, $date);

                        $data[$key]['fee_collection'] = $feeCollection->groupBy(function ($row) { return $this->collectionDateKey($row); });
                        $data[$key]['fee_collection_total'] = $feeCollection->sum('paid_amount');
                        $key = $key;
                    }
                    $data['keys'] = $key;
                    $data['tag'] = $request->report_type;
                    $data['fee_head_tag'] = 'fee_head';
                    $data['url'] = URL::current();
                    $data['row'] = collect($data);
                    $data['date_total_fee'] = $data['row']->sum('fee_collection_total');
                }
                elseif($request->report_type == 'weekly'){
                    $period = CarbonPeriod::create($request->start_date, $request->end_date)->week();
                    foreach ($period as $key => $date) {
                        $data['print_head'] = $this->feeFilterTitle($request->fee_heads).' - WEEKLY';
                        $data[$key]['table_head'] = Carbon::parse($date)->format('m/d/Y') . ' - ' . Carbon::parse($date->clone()->addWeek()->subDay(1))->format('m/d/Y');
                        $feeCollection = $this->dateRangeWithHeadFeeCollection($request->fee_heads,$date,$date->clone()->addWeek()->subDay(1));
                        $data[$key]['fee_collection'] = $feeCollection->groupBy(function ($row) { return $this->collectionDateKey($row); });
                        $data[$key]['fee_collection_total'] = $feeCollection->sum('paid_amount');
                        $key = $key;
                    }
                    $data['keys'] = $key;
                    $data['tag'] = $request->report_type;
                    $data['fee_head_tag'] = 'fee_head';
                    $data['url'] = URL::current();
                    $data['row'] = collect($data);
                    $data['date_total_fee'] = $data['row']->sum('fee_collection_total');
                }
                elseif($request->report_type == 'monthly'){
                    $period = CarbonPeriod::create($request->start_date, $request->end_date)->month();
                    foreach ($period as $key => $date) {
                        $data['print_head'] = $this->feeFilterTitle($request->fee_heads).' - MONTHLY';
                        $data[$key]['table_head'] = Carbon::parse($date)->format('m/d/Y') . ' - ' . Carbon::parse($date->clone()->addMonth()->subDay(1))->format('m/d/Y') ;
                        $feeCollection = $this->dateRangeWithHeadFeeCollection($request->fee_heads, $date,$date->clone()->addMonth()->subDay(1));
                        $data[$key]['fee_collection'] = $feeCollection->groupBy(function ($row) { return $this->collectionDateKey($row); });
                        $data[$key]['fee_collection_total'] = $feeCollection->sum('paid_amount');
                        $key = $key;
                    }
                    $data['keys'] = $key;
                    $data['tag'] = $request->report_type;
                    $data['fee_head_tag'] = 'fee_head';
                    $data['url'] = URL::current();
                    $data['row'] = collect($data);
                    //dd($data['row']);
                    $data['date_total_fee'] = $data['row']->sum('fee_collection_total');
                }
                elseif($request->report_type == 'yearly'){
                    $period = CarbonPeriod::create($request->start_date, $request->end_date)->year();
                    foreach ($period as $key => $date) {
                        $data['print_head'] = $this->feeFilterTitle($request->fee_heads).' - YEARLY';
                        $data[$key]['table_head'] = Carbon::parse($date)->format('m/d/Y') . ' - ' . Carbon::parse($date->clone()->addYear()->subDay(1))->format('m/d/Y');
                        $feeCollection = $this->dateRangeWithHeadFeeCollection($request->fee_heads, $date,$date->clone()->addYear()->subDay(1));
                        $data[$key]['fee_collection'] = $feeCollection->groupBy(function ($row) { return $this->collectionDateKey($row); });
                        $data[$key]['fee_collection_total'] = $feeCollection->sum('paid_amount');
                        $key = $key;
                    }
                    $data['keys'] = $key;
                    $data['tag'] = $request->report_type;
                    $data['fee_head_tag'] = 'fee_head';
                    $data['url'] = URL::current();
                    $data['row'] = collect($data);
                    $data['date_total_fee'] = $data['row']->sum('fee_collection_total');
                }
                else{

                }

            }
            elseif($request->report_type && $request->start_date && $request->end_date) {
                if($request->report_type == 'daily') {
                    $period = CarbonPeriod::create($request->start_date, $request->end_date);
                    foreach ($period as $key => $date) {
                        $data['print_head'] = $this->panel.' - DAILY';
                        $data[$key]['table_head'] = Carbon::parse($date)->format('m/d/Y');
                        $feeCollection = $this->dateFeeCollection($date);
                        $data[$key]['fee_collection'] = $feeCollection->groupBy('fee_head');
                        $data[$key]['fee_collection_total'] = $feeCollection->sum('paid_amount');
                        $key = $key;
                    }
                    $data['keys'] = $key;
                    $data['tag'] = $request->report_type;
                    $data['url'] = URL::current();
                    $data['row'] = collect($data);
                }
                elseif($request->report_type == 'weekly'){
                    $period = CarbonPeriod::create($request->start_date, $request->end_date)->week();
                    foreach ($period as $key => $date) {
                        $data['print_head'] = $this->panel.' - WEEKLY';
                        $data[$key]['table_head'] = Carbon::parse($date)->format('m/d/Y') . ' - ' . Carbon::parse($date->clone()->addWeek()->subDay(1))->format('m/d/Y');
                        $feeCollection = $this->dateRangeFeeCollection($date,$date->clone()->addWeek()->subDay(1));
                        $data[$key]['fee_collection'] = $feeCollection->groupBy('fee_head');
                        $data[$key]['fee_collection_total'] = $feeCollection->sum('paid_amount');
                        $key = $key;
                    }
                    $data['keys'] = $key;
                    $data['tag'] = $request->report_type;
                    $data['url'] = URL::current();
                    $data['row'] = collect($data);
                }
                elseif($request->report_type == 'monthly'){
                    $period = CarbonPeriod::create($request->start_date, $request->end_date)->month();
                    foreach ($period as $key => $date) {
                        $data['print_head'] = $this->panel.' - MONTHLY';
                        $data[$key]['table_head'] = Carbon::parse($date)->format('m/d/Y') . ' - ' . Carbon::parse($date->clone()->addMonth()->subDay(1))->format('m/d/Y') ;
                        $feeCollection = $this->dateRangeFeeCollection($date,$date->clone()->addMonth()->subDay(1));
                        $data[$key]['fee_collection'] = $feeCollection->groupBy('fee_head');
                        $data[$key]['fee_collection_total'] = $feeCollection->sum('paid_amount');
                        $key = $key;
                    }
                    $data['keys'] = $key;
                    $data['tag'] = $request->report_type;
                    $data['url'] = URL::current();
                    $data['row'] = collect($data);
                }
                elseif($request->report_type == 'yearly'){
                    $period = CarbonPeriod::create($request->start_date, $request->end_date)->year();
                    foreach ($period as $key => $date) {
                        $data['print_head'] = $this->panel.' - YEARLY';
                        $data[$key]['table_head'] = Carbon::parse($date)->format('m/d/Y') . ' - ' . Carbon::parse($date->clone()->addYear()->subDay(1))->format('m/d/Y');
                        $feeCollection = $this->dateRangeFeeCollection($date,$date->clone()->addYear()->subDay(1));
                        $data[$key]['fee_collection'] = $feeCollection->groupBy('fee_head');
                        $data[$key]['fee_collection_total'] = $feeCollection->sum('paid_amount');
                        $key = $key;
                    }
                    $data['keys'] = $key;
                    $data['tag'] = $request->report_type;
                    $data['url'] = URL::current();
                    $data['row'] = collect($data);
                }
                else{

                }

            }
            elseif ($request->fee_heads && $request->start_date && $request->end_date) {
                $period = CarbonPeriod::create($request->start_date, $request->end_date);
                foreach ($period as $key => $date) {
                    $data['print_head'] = $this->feeFilterTitle($request->fee_heads).' - DAILY';
                    $data[$key]['table_head'] = Carbon::parse($date)->format('m-d-Y');
                    $feeCollection = $this->dateWithHeadFeeCollection($request->fee_heads, $date);
                    $data[$key]['fee_collection'] = $feeCollection->groupBy(function ($row) { return $this->collectionDateKey($row); });
                    $data[$key]['fee_collection_total'] = $feeCollection->sum('paid_amount');
                    $key = $key;
                }
                $data['keys'] = $key;
                $data['tag'] = 'daily';
                $data['fee_head_tag'] = 'fee_head';
                $data['url'] = URL::current();
                $data['row'] = collect($data);
                $data['date_total_fee'] = $data['row']->sum('fee_collection_total');
            }
            elseif ($request->start_date && $request->end_date) {
                $data['print_head'] = $this->panel.' - ['. Carbon::parse($request->start_date)->format('m/d/Y') . ' - ' . Carbon::parse($request->end_date)->format('m/d/Y') . ']';
                $feeCollection = $this->dateRangeFeeCollection($request->start_date,$request->end_date);
                $data['fee_collection'] = $feeCollection->groupBy('fee_head');
                $data['fee_collection_total'] = $feeCollection->sum('paid_amount');
                $data['tag'] = 'range';
                $data['keys'] = $data['fee_collection']->count();
                $data['row'] = collect($data);
            }
            elseif($request->fee_heads){
                $request->session()->flash($this->message_warning,'Filter With Date Range.');
                $data['tag'] = 'today';
                redirect()->back();

            }
            else{
                $request->session()->flash($this->message_warning,'Filter With Date Range.');
                redirect()->back();
            }

        }else{
            $data['print_head'] = $this->panel.' - '.Carbon::parse($date)->format('m/d/Y');
            $feeCollection = $this->dateFeeCollection($date);
            $data['fee_collection'] = $feeCollection->groupBy('fee_head');
            $data['fee_collection_total'] = $feeCollection->sum('paid_amount');
            $data['tag'] = 'today';
        }


        /* Main Fee Heads offered too, so the office can ask "how much came in for the
           admission fee" without adding up twenty-six separate reports. */
        $data['fee_heads'] = $this->activeFeeHeadWithGroups();
        $data['filter_query'] = $this->filter_query;
        $data['url'] = URL::current();

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    /**
     * Group key for a collection row's date.
     *
     * FeeCollection casts `date` to a Carbon instance, and a Carbon cannot be used as an array
     * key, so groupBy('date') died with "array_key_exists(): The first argument should be
     * either a string or an integer" the moment a head-filtered report actually found rows.
     * The view reads this key back with Carbon::parse(), so a plain Y-m-d string is what it
     * wants - and grouping by the day, not the timestamp, is what "daily" means anyway.
     */
    private function collectionDateKey($row)
    {
        if ($row->date instanceof \DateTimeInterface) {
            return $row->date->format('Y-m-d');
        }

        return Carbon::parse($row->date)->format('Y-m-d');
    }

    /* endOfDay used to live here as a private method. The same day-closing was then needed by
       four more screens, so it moved to DateTimeScope, which this controller already has
       through CollegeBaseController - and a private copy in a subclass of a class that now
       offers it publicly is a fatal error, not an override. Same behaviour, one definition.
       Every caller below is reached only when start_date and end_date are both set. */

    /**
     * A whole fee, head by head: what landed in each of its sub heads over the range.
     *
     * Every sub head is listed, including the ones that received nothing - twenty-six heads
     * that quietly become twenty-three on screen cannot be reconciled against the fee. Rows
     * come back in the fee's own fill order, so college heads sit above department heads and
     * a part payment reads down the page the way the money actually went in.
     */
    public function feeGroupHeadBreakdown($groupId, $start_date, $end_date)
    {
        $group = FeeHeadGroup::with('items.feeHead')->find($groupId);

        if (!$group) {
            return collect();
        }

        /* One grouped query for the money, then matched up in PHP. Asking per head would be
           twenty-six round trips to draw one screen. */
        $paid = FeeCollection::select('fm.fee_head', DB::raw('SUM(fee_collections.paid_amount) as paid'))
            ->join('fee_masters as fm','fm.id','=','fee_collections.fee_masters_id')
            ->where('fee_collections.status', 1)
            ->whereBetween('fee_collections.date', [$start_date, $this->endOfDay($end_date)])
            ->where(function ($query) use ($groupId) {
                $query->where('fm.billing_period_key', 'GROUP-'.$groupId)
                      ->orWhere('fm.billing_period_key', 'like', 'GROUP-'.$groupId.'-%');
            })
            ->groupBy('fm.fee_head')
            ->pluck('paid', 'fee_head');

        $rows = collect();

        foreach ($group->items as $item) {
            $rows->push((object) [
                'fee_head'     => $item->fee_head_id,
                'title'        => optional($item->feeHead)->fee_head_title ?? 'Unknown Head',
                'collected_by' => optional($item->feeHead)->collected_by ?? 'college',
                'fee_amount'   => (float) $item->amount,
                'amount'       => (float) ($paid[$item->fee_head_id] ?? 0),
            ]);
        }

        return $rows;
    }

    /**
     * The department list as a file, so it can be worked on rather than only looked at.
     *
     * Excel by default, CSV on request. Same query as the screen, so the file and the sheet can
     * never say different things - the numbers are not recomputed here, only formatted.
     */
    public function feeGroupDepartmentExport(Request $request)
    {
        $groupId = $this->feeHeadGroupIdFromFilter($request->get('fee_heads'));

        if (!$groupId || !$request->get('start_date') || !$request->get('end_date')) {
            $request->session()->flash($this->message_warning,
                'Choose a Main Fee Head and a date range first.');
            return redirect()->route($this->base_route);
        }

        $rows = $this->feeGroupStudentsByDepartment(
            $groupId, $request->get('start_date'), $request->get('end_date'));

        if (!$rows->count()) {
            $request->session()->flash($this->message_warning,
                'Nothing was collected against this fee in that period.');
            return redirect()->back();
        }

        $title = $this->feeFilterTitle($request->get('fee_heads'));
        $period = Carbon::parse($request->get('start_date'))->format('d M Y')
            . ' to ' . Carbon::parse($request->get('end_date'))->format('d M Y');

        $heads = $this->feeGroupHeadBreakdown(
            $groupId, $request->get('start_date'), $request->get('end_date'));
        $collegeTotal = $heads->where('collected_by', '!=', 'department')->sum('amount');
        $departmentTotal = $heads->where('collected_by', 'department')->sum('amount');

        /* A file name that says what is in it and for when, so a folder of these stays usable. */
        $name = 'Students-by-Department_'
            . preg_replace('/[^A-Za-z0-9]+/', '-', $title) . '_'
            . Carbon::parse($request->get('start_date'))->format('d-m-Y') . '_to_'
            . Carbon::parse($request->get('end_date'))->format('d-m-Y');

        $export = new FeeGroupDepartmentExport($rows, $title, $period,
            $collegeTotal, $departmentTotal, $heads->sum('amount'));

        if (strtolower((string) $request->get('format')) === 'csv') {
            return Excel::download($export, $name . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download($export, $name . '.xlsx');
    }

    /**
     * Whose money the head-wise total is made of, department by department.
     *
     * Two counts, not one, because they answer different questions and are rarely the same:
     * how many students paid anything towards this fee, and how many of those paid its
     * department part. Where a head divided by its rate does not land on a whole number of
     * students, the gap between these two columns is usually the reason.
     *
     * Same filter as the head breakdown - status 1, the same dates, the same GROUP-n key - so
     * the two tables on the sheet always add up to each other.
     */
    public function feeGroupStudentsByDepartment($groupId, $start_date, $end_date)
    {
        $deptHeadIds = DB::table('fee_head_group_items as i')
            ->leftJoin('fee_heads as h', 'h.id', '=', 'i.fee_head_id')
            ->where('i.fee_head_group_id', $groupId)
            ->where('i.status', 1)
            ->where('h.collected_by', 'department')
            ->pluck('i.fee_head_id')
            ->all();

        /* fee_masters.fee_head holds the head id as text, so the list is quoted to match it
           rather than forcing MySQL to cast the column on every row. Built from ints taken from
           our own table - nothing here comes from the request. */
        $deptList = $deptHeadIds
            ? implode(',', array_map(function ($id) { return "'" . (int) $id . "'"; }, $deptHeadIds))
            : "''";

        return DB::table('fee_collections as c')
            ->join('fee_masters as fm', 'fm.id', '=', 'c.fee_masters_id')
            ->join('students as s', 's.id', '=', 'c.students_id')
            ->leftJoin('faculties as f', 'f.id', '=', 's.faculty')
            ->where('c.status', 1)
            ->whereBetween('c.date', [$start_date, $this->endOfDay($end_date)])
            ->where(function ($query) use ($groupId) {
                $query->where('fm.billing_period_key', 'GROUP-' . $groupId)
                      ->orWhere('fm.billing_period_key', 'like', 'GROUP-' . $groupId . '-%');
            })
            ->select(
                DB::raw("COALESCE(NULLIF(TRIM(f.faculty), ''), 'Not set') as department"),
                DB::raw('COUNT(DISTINCT c.students_id) as students'),
                DB::raw("COUNT(DISTINCT CASE WHEN fm.fee_head IN ({$deptList}) THEN c.students_id END) as dept_students"),
                DB::raw("SUM(CASE WHEN fm.fee_head IN ({$deptList}) THEN 0 ELSE c.paid_amount END) as college_amount"),
                DB::raw("SUM(CASE WHEN fm.fee_head IN ({$deptList}) THEN c.paid_amount ELSE 0 END) as department_amount"),
                DB::raw('SUM(c.paid_amount) as total_amount')
            )
            ->groupBy('department')
            ->orderBy('department')
            ->get();
    }

    //with fee head & range
    public function dateRangeWithHeadFeeCollection($head, $start_date, $end_date)
    {
        $query = FeeCollection::select('fee_collections.date', 'fee_collections.discount', 'fee_collections.fine', 'fee_collections.paid_amount',
            'fee_collections.payment_method','fee_collections.note',
            'fm.status as fm_status','fm.fee_head')
            ->where('fee_collections.paid_amount', '>',0)
            ->whereBetween('fee_collections.date', [$start_date, $this->endOfDay($end_date)])
            ->join('fee_masters as fm','fm.id','=','fee_collections.fee_masters_id')
            ->where('fee_collections.status', 1)
            ->orderBy('fee_collections.created_at','desc');

        /* One head, or every sub head of a Main Fee Head - the filter decides which. */
        $feeCollection = $this->applyFeeHeadFilter($query, $head)->get();

        return $feeCollection;

    }

    //with head & single date
    public function dateWithHeadFeeCollection($head,$date)
    {
        $query = FeeCollection::select('fee_collections.fee_masters_id', 'fee_collections.date',
            'fee_collections.discount', 'fee_collections.fine', 'fee_collections.paid_amount',
            'fm.status as fm_status','fm.fee_head')
            ->where('fee_collections.paid_amount', '>',0)
            ->whereDate('fee_collections.date', '=', $date)
            ->join('fee_masters as fm','fm.id','=','fee_collections.fee_masters_id')
            ->where('fee_collections.status', 1)
            ->orderBy('fee_collections.date','desc');

        /* One head, or every sub head of a Main Fee Head - the filter decides which. */
        $feeCollection = $this->applyFeeHeadFilter($query, $head)->get();

        return $feeCollection;
    }

    //date range
    public function dateRangeFeeCollection($start_date, $end_date)
    {
        $feeCollection = FeeCollection::select('fee_collections.students_id',
            'fee_collections.date', 'fee_collections.discount', 'fee_collections.fine', 'fee_collections.paid_amount',
            'fee_collections.payment_method','fee_collections.note',
            'fm.status as fm_status','fm.fee_head')
            ->whereBetween('fee_collections.date', [$start_date, $this->endOfDay($end_date)])
            ->join('fee_masters as fm','fm.id','=','fee_collections.fee_masters_id')
            ->where('fee_collections.status', 1)
            ->orderBy('fee_collections.date','desc')
            ->get();

        return $feeCollection;
    }

    //single date
    public function dateFeeCollection($date)
    {
        $feeCollection = FeeCollection::select('fee_collections.students_id', 'fee_collections.fee_masters_id', 'fee_collections.date',
            'fee_collections.discount', 'fee_collections.fine', 'fee_collections.paid_amount',
            'fm.status as fm_status','fm.fee_head')
            ->whereDate('fee_collections.date', '=', $date)
            ->join('fee_masters as fm','fm.id','=','fee_collections.fee_masters_id')
            ->where('fee_collections.status', 1)
            ->orderBy('fee_collections.date','desc')
            ->get();

        return $feeCollection;


    }

}
