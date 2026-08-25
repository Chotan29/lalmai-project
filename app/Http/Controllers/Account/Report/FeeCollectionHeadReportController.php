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
use Illuminate\Support\Facades\Schema;
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
            $feeGroupIds = $this->feeGroupIdsFromFilter($request->fee_heads);

            /*
             * A fee and a plain head cannot be added together.
             *
             * A Main Fee Head is answered head by head; a plain head is answered date by date.
             * Combined there is no single question being asked, and the sheet would silently drop
             * one of them - which it did, before this said so.
             */
            if ($feeGroupIds && $this->feeFilterHasPlainHead($request->fee_heads)) {
                $request->session()->flash($this->message_warning,
                    'A Main Fee Head and an ordinary fee head cannot be reported together.'
                    . ' Choose either one or more Main Fee Heads, or a single head.');

                return redirect()->route($this->base_route);
            }

            /*
             * A Main Fee Head no longer needs a date range typed in.
             *
             * Asking for one was reasonable while this was a date report, but a fee like Admission
             * 2025-2026 is collected over a season and the office does not know the day the first
             * receipt was written. Left empty, the range is taken from the money itself - the
             * first and last collection against this fee - so the sheet still covers a stated
             * period rather than a vague "everything", and still says on its face what that
             * period was.
             */
            if ($feeGroupIds && (!$request->start_date || !$request->end_date)) {
                /* Several fees: the earliest start and latest end across all of them, so the
                   period printed at the top covers every figure below it. */
                $span = $this->feeGroupDateSpan($feeGroupIds);
                if ($span) {
                    $request->merge([
                        'start_date' => $request->start_date ?: $span->first_date,
                        'end_date'   => $request->end_date   ?: $span->last_date,
                    ]);
                    $data['fg_whole_period'] = true;
                }
            }

            if($feeGroupIds && $request->start_date && $request->end_date) {
                /* Title and period kept apart as well as joined: the printed sheet sets them on
                   separate lines, and splitting a formatted string back up in the view is how
                   headings end up mangled. print_head stays for anything already using it. */
                $data['fee_title'] = $this->feeFilterTitle($request->fee_heads);
                $data['fg_period'] = Carbon::parse($request->start_date)->format('d M Y')
                    . '  to  ' . Carbon::parse($request->end_date)->format('d M Y');
                $data['print_head'] = $data['fee_title'] . ' - [' . $data['fg_period'] . ']';

                /*
                 * The same sheet for one programme instead of all of them.
                 *
                 * Left empty the report is exactly what it always was. Choose a programme and
                 * every figure below is that programme's students only - including their share of
                 * the college heads, which everybody pays. That share is a real number and a
                 * useful one, but it is not "the college total", so the headings say whose it is.
                 */
                $data['fg_programmes'] = $this->feeGroupProgrammes(
                    $feeGroupIds, $request->start_date, $request->end_date);

                $facultyId = (int) $request->get('programme', 0) ?: null;
                if ($facultyId && !$data['fg_programmes']->has($facultyId)) {
                    $facultyId = null;   // asked for a programme with nothing in this period
                }
                $data['fg_programme_id']    = $facultyId;
                $data['fg_programme_title'] = $facultyId ? $data['fg_programmes'][$facultyId] : null;
                $this->filter_query['programme'] = $facultyId;

                if ($facultyId) {
                    $data['print_head'] = $data['fee_title'] . ' - ' . $data['fg_programme_title']
                        . ' - [' . $data['fg_period'] . ']';
                }

                $data['fee_group_rows'] = $this->feeGroupHeadBreakdown(
                    $feeGroupIds, $request->start_date, $request->end_date, $facultyId);
                $data['fee_collection_total'] = $data['fee_group_rows']->sum('amount');
                $data['college_total'] = $data['fee_group_rows']->where('collected_by','!=','department')->sum('amount');
                $data['department_total'] = $data['fee_group_rows']->where('collected_by','department')->sum('amount');
                /* Whose money this is. A head-wise total answers "how much" but not "for how
                   many", and the two are only reconcilable together: a head divided by its rate
                   should land on the number of students, and where it does not the department
                   list is what says which. */
                $data['fee_group_departments'] = $this->feeGroupStudentsByDepartment(
                    $feeGroupIds, $request->start_date, $request->end_date, $facultyId);

                /*
                 * The assumption several fees rest on, checked rather than trusted.
                 *
                 * Choosing several fees only adds up if no student paid into more than one of
                 * them - they are meant to be the department-wise halves of one admission, and a
                 * student sits in one department. Pick two fees that do share students and the
                 * totals silently count that student twice, which is exactly the sort of thing
                 * nobody notices until the bank does.
                 */
                $data['fg_fee_ids']  = $feeGroupIds;
                $data['fg_overlap']  = count($feeGroupIds) > 1
                    ? $this->studentsInMoreThanOneFee($feeGroupIds, $request->start_date, $request->end_date)
                    : 0;

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

        /* What the box should come back showing. Read from the filter rather than left to the
           form helper: the filter holds one comma separated string and the box is now a multiple
           select, so nothing would match and every choice would be forgotten on reload. */
        $data['fee_heads_selected'] = array_map(function ($id) {
            return 'GROUP:' . $id;
        }, $this->feeGroupIdsFromFilter($request->get('fee_heads')));

        if (!$data['fee_heads_selected'] && $request->get('fee_heads')) {
            $data['fee_heads_selected'] = [$request->get('fee_heads')];
        }

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
    public function feeGroupHeadBreakdown($groupId, $start_date, $end_date, $facultyId = null)
    {
        $groupIds = $this->asGroupIds($groupId);

        $items = $this->mergedGroupItems($groupIds);

        if (!$items->count()) {
            return collect();
        }

        /* One grouped query for the money, then matched up in PHP. Asking per head would be
           twenty-six round trips to draw one screen. */
        $query = FeeCollection::select('fm.fee_head', DB::raw('SUM(fee_collections.paid_amount) as paid'))
            ->join('fee_masters as fm','fm.id','=','fee_collections.fee_masters_id')
            ->where('fee_collections.status', 1)
            ->whereBetween('fee_collections.date', [$start_date, $this->endOfDay($end_date)]);

        $this->applyFeeGroups($query, $groupIds);

        /* One programme only. The students table is joined solely for this - the whole-college
           sheet has no need of it, and joining it always would cost every reader a join to
           answer a question only some of them asked. */
        if ($facultyId) {
            $query->join('students as s', 's.id', '=', 'fee_collections.students_id')
                  ->where('s.faculty', $facultyId);
        }

        $paid = $query->groupBy('fm.fee_head')->pluck('paid', 'fee_head');

        /*
         * Money given back, head by head.
         *
         * A student who paid and then did not take admission is refunded, and the refund is stored
         * broken down the same way the payment was - one line per sub head. Without subtracting it
         * here the sheet would keep showing income the college has already handed back, and the
         * department heads would be the ones reporting money they do not have.
         *
         * Taken off the head rather than hidden: the refunded figure is carried alongside so the
         * two can still be told apart on the page.
         */
        $refunded = collect();

        /* Only ask if the tables are there - this report has to open on an installation that has
           never recorded a refund. Checked rather than caught, so a genuine mistake in the query
           below still surfaces instead of being swallowed into a silent zero. */
        if (Schema::hasTable('payment_refund_items')) {
            $refundQuery = DB::table('payment_refund_items as ri')
                ->join('payment_refunds as r', 'r.id', '=', 'ri.payment_refund_id')
                ->join('fee_collections as c', 'c.id', '=', 'ri.fee_collection_id')
                ->join('fee_masters as fm', 'fm.id', '=', 'c.fee_masters_id')
                ->where('r.status', 1)
                ->whereBetween('ri.date', [$start_date, $this->endOfDay($end_date)]);

            $this->applyFeeGroups($refundQuery, $groupIds);

            /* The refund has to be narrowed the same way the collection was, or one programme's
               sheet would have another programme's refunds taken off it. */
            if ($facultyId) {
                $refundQuery->join('students as s2', 's2.id', '=', 'c.students_id')
                            ->where('s2.faculty', $facultyId);
            }

            $refunded = $refundQuery
                ->groupBy('ri.fee_head')
                /* Aliased. pluck() names its columns from what comes back, so a raw SUM() and a
                   dotted key give a collection keyed on nothing that matches - it returns quietly
                   empty and the refund appears to have had no effect at all. */
                ->select('ri.fee_head as head', DB::raw('SUM(ri.amount) as given'))
                ->pluck('given', 'head');
        }

        $rows = collect();

        foreach ($items as $item) {
            $collected = (float) ($paid[$item->fee_head_id] ?? 0);
            $back      = (float) ($refunded[$item->fee_head_id] ?? 0);

            $rows->push((object) [
                'fee_head'     => $item->fee_head_id,
                'title'        => $item->title,
                'collected_by' => $item->collected_by,
                'fee_amount'   => $item->fee_amount,
                'collected'    => $collected,
                'refunded'     => $back,
                /* What the head is actually left holding - this is the column everything totals. */
                'amount'       => round($collected - $back, 2),
            ]);
        }

        return $rows;
    }

    /**
     * How many students paid into more than one of the chosen fees.
     *
     * Zero is the answer the report needs. The fees meant to be chosen together are one admission
     * split by department, and a student belongs to one department - so their money appears under
     * exactly one of them and the totals add up. Anything above zero means the reader has combined
     * fees that overlap, and every figure on the sheet counts those students twice.
     *
     * Counted rather than assumed, because nothing stops somebody picking an admission fee and a
     * form fill-up fee together, and the sheet would look perfectly reasonable.
     */
    protected function studentsInMoreThanOneFee(array $groupIds, $start_date, $end_date)
    {
        if (count($groupIds) < 2) {
            return 0;
        }

        /* One row per student per fee, then the students with more than one row. The fee is
           identified by its own group id rather than by the billing key, so a recurring run's
           dated key ("GROUP-3-2026-08") counts as the same fee as a one-off "GROUP-3". */
        $cases = [];
        foreach ($groupIds as $id) {
            $id = (int) $id;
            $cases[] = "MAX(CASE WHEN fm.billing_period_key = 'GROUP-{$id}'"
                . " OR fm.billing_period_key LIKE 'GROUP-{$id}-%' THEN 1 ELSE 0 END)";
        }

        $sum = implode(' + ', $cases);

        $query = DB::table('fee_collections as c')
            ->join('fee_masters as fm', 'fm.id', '=', 'c.fee_masters_id')
            ->where('c.status', 1)
            ->whereBetween('c.date', [$start_date, $this->endOfDay($end_date)]);

        $this->applyFeeGroups($query, $groupIds);

        return $query->groupBy('c.students_id')
            ->havingRaw("({$sum}) > 1")
            ->get(['c.students_id'])
            ->count();
    }

    /**
     * The sub heads of one or more fees, merged into a single list.
     *
     * A head that appears in two of the chosen fees becomes one row, not two: it is one head with
     * one bank account, and the money in it is the same money whichever fee charged it. Splitting
     * it would give the bank two lines for one account and the reader two numbers to add up by
     * hand.
     *
     * The order is the first fee's own fill order, with anything only the later fees have appended
     * in theirs - so a single fee reads exactly as it always did, and a second fee adds to the
     * bottom rather than shuffling what was above it.
     *
     * The rate is only carried when every fee charging that head charges the same. Where they
     * differ there is no single rate to print, and printing one of them would invite the reader to
     * multiply it by the student count and find the total does not match.
     */
    protected function mergedGroupItems(array $groupIds)
    {
        if (!$groupIds) {
            return collect();
        }

        $groups = FeeHeadGroup::with('items.feeHead')->whereIn('id', $groupIds)->get()->keyBy('id');

        $merged = [];

        foreach ($groupIds as $groupId) {
            $group = $groups->get($groupId);
            if (!$group) { continue; }

            foreach ($group->items as $item) {
                $headId = $item->fee_head_id;

                if (!isset($merged[$headId])) {
                    $merged[$headId] = (object) [
                        'fee_head_id'  => $headId,
                        'title'        => optional($item->feeHead)->fee_head_title ?? 'Unknown Head',
                        'collected_by' => optional($item->feeHead)->collected_by ?? 'college',
                        'fee_amount'   => (float) $item->amount,
                        'rates_agree'  => true,
                    ];
                    continue;
                }

                if (abs($merged[$headId]->fee_amount - (float) $item->amount) > 0.005) {
                    $merged[$headId]->rates_agree = false;
                    $merged[$headId]->fee_amount  = null;
                }
            }
        }

        return collect(array_values($merged));
    }

    /**
     * The department list as a file, so it can be worked on rather than only looked at.
     *
     * Excel by default, CSV on request. Same query as the screen, so the file and the sheet can
     * never say different things - the numbers are not recomputed here, only formatted.
     */
    public function feeGroupDepartmentExport(Request $request)
    {
        $groupIds = $this->feeGroupIdsFromFilter($request->get('fee_heads'));

        if (!$groupIds || !$request->get('start_date') || !$request->get('end_date')) {
            $request->session()->flash($this->message_warning,
                'Choose a Main Fee Head and a date range first.');
            return redirect()->route($this->base_route);
        }

        $rows = $this->feeGroupStudentsByDepartment(
            $groupIds, $request->get('start_date'), $request->get('end_date'),
            (int) $request->get('programme', 0) ?: null);

        if (!$rows->count()) {
            $request->session()->flash($this->message_warning,
                'Nothing was collected against this fee in that period.');
            return redirect()->back();
        }

        $title = $this->feeFilterTitle($request->get('fee_heads'));
        $period = Carbon::parse($request->get('start_date'))->format('d M Y')
            . ' to ' . Carbon::parse($request->get('end_date'))->format('d M Y');

        $heads = $this->feeGroupHeadBreakdown(
            $groupIds, $request->get('start_date'), $request->get('end_date'));
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
     * The bank transfer letters for whatever is on the report right now.
     *
     * Same fee, same period, same programme - the file and the sheet cannot disagree, because the
     * money is read again with the same filters rather than carried over from the screen.
     *
     * The letter is not the head table with account numbers added. A head can pay into several
     * accounts and several heads can pay into one, so the money is regrouped by account first;
     * BankTransferLetter does that.
     */
    public function bankLetter(Request $request)
    {
        $groupIds = $this->feeGroupIdsFromFilter($request->get('fee_heads'));

        if (!$groupIds) {
            $request->session()->flash($this->message_warning, 'Choose a Main Fee Head first.');
            return redirect()->route($this->base_route);
        }

        /*
         * Several fees on one letter is fine - it is one transfer out of one source account - but
         * only while no student sits in two of them. If one does, their money is counted twice and
         * the bank is asked to move more than the college holds. Refused rather than warned about:
         * a warning on a downloaded file is a warning nobody reads.
         */
        $overlap = $this->studentsInMoreThanOneFee($groupIds,
            $request->get('start_date') ?: '2000-01-01', $request->get('end_date') ?: '2100-01-01');

        if ($overlap) {
            $request->session()->flash($this->message_warning,
                $overlap . ' student(s) appear in more than one of the chosen fees, so their money'
                . ' would be transferred twice. Choose fees that do not share students.');
            return redirect()->back();
        }

        /* Dates are optional here as well - left empty the whole fee is covered. */
        $start = $request->get('start_date');
        $end   = $request->get('end_date');

        if (!$start || !$end) {
            $span = $this->feeGroupDateSpan($groupIds);
            if (!$span) {
                $request->session()->flash($this->message_warning,
                    'Nothing has been collected against this fee yet.');
                return redirect()->back();
            }
            $start = $start ?: $span->first_date;
            $end   = $end   ?: $span->last_date;
        }

        $facultyId = (int) $request->get('programme', 0) ?: null;

        $letter = app(\App\Services\Fees\BankTransferLetter::class)
            ->build($groupIds, $start, $end, $facultyId);

        /*
         * A head holding money with nowhere to send it stops the letter.
         *
         * The alternative is a letter whose total is short by that amount, which the bank will
         * question and nobody at the college will be able to explain. Better to be sent back to
         * the bank account screen now.
         */
        if (!empty($letter->orphans)) {
            $names = collect($letter->orphans)->map(function ($o) {
                return $o->title . ' (' . number_format($o->amount, 2) . ')';
            })->implode(', ');

            $request->session()->flash($this->message_warning,
                'These heads have money but no bank account, so the letter would not add up: '
                . $names . '. Set their accounts first.');

            return redirect()->back();
        }

        if (!count($letter->college) && !count($letter->department)) {
            $request->session()->flash($this->message_warning,
                'Nothing was collected against this fee in that period.');
            return redirect()->back();
        }

        $heading = $this->feeFilterTitle($request->get('fee_heads'));
        if ($facultyId) {
            $programme = \App\Models\Faculty::where('id', $facultyId)->value('faculty');
            if ($programme) { $heading .= ' (' . $programme . ')'; }
        }

        $name = 'Bank-Transfer_'
            . preg_replace('/[^A-Za-z0-9]+/', '-', $heading) . '_'
            . Carbon::parse($start)->format('d-m-Y') . '_to_' . Carbon::parse($end)->format('d-m-Y');

        return Excel::download(
            new \App\Exports\BankTransferLetterExport($letter, $heading), $name . '.xlsx');
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
    /**
     * The programmes that actually have money in this fee, in this period.
     *
     * Not every programme in the college - only the ones with something to show. A dropdown
     * listing thirty departments when three of them collected anything sends the reader looking
     * for a fault every time they pick one of the twenty-seven empty ones.
     *
     * @return \Illuminate\Support\Collection  faculty id => programme name
     */
    /**
     * The first and last day money was taken against this fee.
     *
     * Used when the reader gives no dates. A report with no period at all cannot be filed or
     * checked against anything; one that states the period it found can.
     */
    public function feeGroupDateSpan($groupId)
    {
        /* Several fees: the earliest start and the latest end across all of them, so the stated
           period actually covers every figure on the sheet. */
        $query = DB::table('fee_collections as c')
            ->join('fee_masters as fm', 'fm.id', '=', 'c.fee_masters_id')
            ->where('c.status', 1);

        $this->applyFeeGroups($query, $this->asGroupIds($groupId));

        $row = $query
            ->select(DB::raw('MIN(c.date) as first_date'), DB::raw('MAX(c.date) as last_date'))
            ->first();

        return ($row && $row->first_date) ? $row : null;
    }

    public function feeGroupProgrammes($groupId, $start_date, $end_date)
    {
        $query = DB::table('fee_collections as c')
            ->join('fee_masters as fm', 'fm.id', '=', 'c.fee_masters_id')
            ->join('students as s', 's.id', '=', 'c.students_id')
            ->join('faculties as f', 'f.id', '=', 's.faculty')
            ->where('c.status', 1)
            ->whereBetween('c.date', [$start_date, $this->endOfDay($end_date)]);

        $this->applyFeeGroups($query, $this->asGroupIds($groupId));

        return $query
            ->distinct()
            ->orderBy('f.faculty')
            ->pluck('f.faculty', 'f.id');
    }

    public function feeGroupStudentsByDepartment($groupId, $start_date, $end_date, $facultyId = null)
    {
        $groupIds = $this->asGroupIds($groupId);

        /* Department heads across every chosen fee. Distinct, because a head that sits in two of
           them is still one head and must not be counted twice in the split below. */
        $deptHeadIds = DB::table('fee_head_group_items as i')
            ->leftJoin('fee_heads as h', 'h.id', '=', 'i.fee_head_id')
            ->whereIn('i.fee_head_group_id', $groupIds ?: [0])
            ->where('i.status', 1)
            ->where('h.collected_by', 'department')
            ->distinct()
            ->pluck('i.fee_head_id')
            ->all();

        /* fee_masters.fee_head holds the head id as text, so the list is quoted to match it
           rather than forcing MySQL to cast the column on every row. Built from ints taken from
           our own table - nothing here comes from the request. */
        $deptList = $deptHeadIds
            ? implode(',', array_map(function ($id) { return "'" . (int) $id . "'"; }, $deptHeadIds))
            : "''";

        $query = DB::table('fee_collections as c')
            ->join('fee_masters as fm', 'fm.id', '=', 'c.fee_masters_id')
            ->join('students as s', 's.id', '=', 'c.students_id')
            ->leftJoin('faculties as f', 'f.id', '=', 's.faculty')
            ->where('c.status', 1)
            ->whereBetween('c.date', [$start_date, $this->endOfDay($end_date)]);

        $this->applyFeeGroups($query, $groupIds);

        /* One programme: the list becomes a single row, which is what makes the two tables on the
           sheet still add up to each other. */
        if ($facultyId) {
            $query->where('s.faculty', $facultyId);
        }

        $rows = $query
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

        /*
         * Take the refunds off here too.
         *
         * The head table already subtracts them. If this one does not, the two tables on the same
         * sheet disagree by exactly the amount refunded - which is how the Management column came
         * to read 581,650 against the head table's 574,250, with nothing on the page to say why.
         * Both are meant to be the same money counted two ways round.
         */
        if (Schema::hasTable('payment_refund_items') && $rows->count()) {
            $refundQuery = DB::table('payment_refund_items as ri')
                ->join('payment_refunds as r', 'r.id', '=', 'ri.payment_refund_id')
                ->join('fee_collections as c', 'c.id', '=', 'ri.fee_collection_id')
                ->join('fee_masters as fm', 'fm.id', '=', 'c.fee_masters_id')
                ->join('students as s', 's.id', '=', 'c.students_id')
                ->leftJoin('faculties as f', 'f.id', '=', 's.faculty')
                ->where('r.status', 1)
                ->whereBetween('ri.date', [$start_date, $this->endOfDay($end_date)]);

            $this->applyFeeGroups($refundQuery, $groupIds);

            if ($facultyId) {
                $refundQuery->where('s.faculty', $facultyId);
            }

            $back = $refundQuery
                ->select(
                    DB::raw("COALESCE(NULLIF(TRIM(f.faculty), ''), 'Not set') as department"),
                    DB::raw("SUM(CASE WHEN fm.fee_head IN ({$deptList}) THEN 0 ELSE ri.amount END) as college_back"),
                    DB::raw("SUM(CASE WHEN fm.fee_head IN ({$deptList}) THEN ri.amount ELSE 0 END) as department_back"),
                    DB::raw('SUM(ri.amount) as total_back')
                )
                ->groupBy('department')
                ->get()
                ->keyBy('department');

            foreach ($rows as $row) {
                $r = $back->get($row->department);
                if (!$r) { continue; }

                $row->college_amount    = round($row->college_amount - $r->college_back, 2);
                $row->department_amount = round($row->department_amount - $r->department_back, 2);
                $row->total_amount      = round($row->total_amount - $r->total_back, 2);
                $row->refunded          = round($r->total_back, 2);
            }
        }

        return $rows;
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
