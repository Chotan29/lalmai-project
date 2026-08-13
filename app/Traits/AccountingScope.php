<?php
namespace App\Traits;

use App\Models\AccountCategory;
use App\Models\Addressinfo;
use App\Models\AlertSetting;
use App\Models\Assets;
use App\Models\Bank;
use App\Models\FeeCollection;
use App\Models\FeeHead;
use App\Models\FeeHeadGroup;
use App\Models\FeeMaster;
use App\Models\PaymentMethod;
use App\Models\PayrollHead;
use App\Models\Student;
use App\Models\TransactionHead;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait AccountingScope{

    //use SmsEmailScope;
    //todo:

    /*These five are called from inside table rows. A receive-history sheet of 5,782 receipts
      asked which fee head each one was 5,782 times, for about a dozen distinct answers. The
      little store that stops that is written once, in RequestLookupCache, rather than a sixth
      time here.*/
    use RequestLookupCache;

    /**
     * One row, fetched once per request, whatever the id is asked for.
     *
     * A missing id still gets remembered - as null - so a bad reference costs one query rather
     * than one per row, and still answers "Unknown" exactly as it did before.
     */
    private function accountingRow($prefix, $id, callable $load)
    {
        $id = (int) $id;

        if ($id <= 0) {
            return null;
        }

        return $this->lookupOnce($prefix . ':' . $id, function () use ($id, $load) {
            return $load($id);
        });
    }

    public function getFeeHeadById($id)
    {
        $feeHead = $this->accountingRow('fee_head', $id, function ($id) {
            return FeeHead::find($id);
        });

        return $feeHead ? $feeHead->fee_head_title : "Unknown";
    }

    public function getTransactionHeadById($id)
    {
        $trHead = $this->accountingRow('tr_head', $id, function ($id) {
            return TransactionHead::find($id);
        });

        return $trHead ? $trHead->tr_head : "Unknown";
    }

    public function getPayrollHeadById($id)
    {
        $payrollHead = $this->accountingRow('payroll_head', $id, function ($id) {
            return PayrollHead::find($id);
        });

        return $payrollHead ? $payrollHead->title : "Unknown";
    }

    public function getBankNameById($id)
    {
        $bank = $this->accountingRow('bank', $id, function ($id) {
            return Bank::find($id);
        });

        return $bank ? $bank->bank_name : "Unknown";
    }

    public function getAcGroupById($id)
    {
        $ac = $this->accountingRow('ac_group', $id, function ($id) {
            return AccountCategory::find($id);
        });

        return $ac ? $ac->ac_name : "Unknown";
    }

    /**
     * A student's fees as they should be read, one line per fee rather than one per head.
     *
     * A Main Fee Head is 26 rows in the accounts but a single charge to the student. Printing
     * all 26 tells a parent nothing: they were billed one admission fee, not twenty-six little
     * ones. The heads stay exactly as they are underneath - collection, the ledger and every
     * report still read them - this only decides what a fee list prints.
     *
     * Shared rather than written per screen, because the office profile and the student's own
     * panel showing different figures for the same fee is precisely the bug worth designing out.
     *
     * @param  \Illuminate\Support\Collection  $feeMasters
     * @return \Illuminate\Support\Collection  rows carrying label, amounts and the ids behind them
     */
    public function feeRowsFromMasters($feeMasters)
    {
        $rows = collect();
        $grouped = [];

        /* Every receipt for these charges, fetched once and totalled per charge.
           Asking each charge for its own sums meant three round trips per row, which was
           tolerable while a student carried one admission charge and became seventy-eight
           queries the moment that charge became twenty-six. */
        $money = [];
        $masterIds = collect($feeMasters)->pluck('id')->filter()->all();

        if ($masterIds) {
            $sums = FeeCollection::whereIn('fee_masters_id', $masterIds)
                ->where('status', 1)
                ->select('fee_masters_id',
                    DB::raw('SUM(paid_amount) as paid_sum'),
                    DB::raw('SUM(discount) as discount_sum'),
                    DB::raw('SUM(fine) as fine_sum'))
                ->groupBy('fee_masters_id')
                ->get();

            foreach ($sums as $s) {
                $money[$s->fee_masters_id] = [
                    'paid'     => (float) $s->paid_sum,
                    'discount' => (float) $s->discount_sum,
                    'fine'     => (float) $s->fine_sum,
                ];
            }
        }

        /* A charge with no receipt simply has nothing against it. */
        $moneyFor = function ($id) use ($money) {
            return isset($money[$id]) ? $money[$id] : ['paid' => 0.0, 'discount' => 0.0, 'fine' => 0.0];
        };

        /* The fee titles, also fetched once rather than per charge. */
        $feeTitles = [];

        /* Head titles for the ungrouped rows, in one query. getFeeHeadById() does a find()
           each time it is called, which is another query per line drawn. */
        $headTitles = FeeHead::whereIn('id', collect($feeMasters)->pluck('fee_head')->filter()->unique()->all())
            ->pluck('fee_head_title', 'id')->all();

        foreach ($feeMasters as $feemaster) {
            $key = $feemaster->billing_period_key;
            $paid = $moneyFor($feemaster->id);

            if ($key && strpos($key, 'GROUP-') === 0) {
                if (!isset($grouped[$key])) {
                    if (!array_key_exists($key, $feeTitles)) {
                        $g = FeeHeadGroup::find((int) substr($key, 6));
                        $feeTitles[$key] = $g ? $g->title : null;
                    }
                    $group = $feeTitles[$key] ? (object) ['title' => $feeTitles[$key]] : null;

                    $grouped[$key] = (object) [
                        'is_group'   => true,
                        'label'      => $group ? $group->title : 'Fee Package',
                        'semester'   => $feemaster->semester,
                        'due_date'   => $feemaster->fee_due_date,
                        'amount'     => 0,
                        'discount'   => 0,
                        'fine'       => 0,
                        'paid'       => 0,
                        'head_count' => 0,
                        'ids'        => [],
                    ];
                    $rows->push($grouped[$key]);
                }

                $row = $grouped[$key];
                $row->amount   += (float) $feemaster->fee_amount;
                $row->discount += $paid['discount'];
                $row->fine     += $paid['fine'];
                $row->paid     += $paid['paid'];
                $row->head_count++;
                $row->ids[] = $feemaster->id;

                continue;
            }

            $rows->push((object) [
                'is_group'   => false,
                'label'      => $headTitles[$feemaster->fee_head] ?? 'Unknown',
                'semester'   => $feemaster->semester,
                'due_date'   => $feemaster->fee_due_date,
                'amount'     => (float) $feemaster->fee_amount,
                'discount'   => $paid['discount'],
                'fine'       => $paid['fine'],
                'paid'       => $paid['paid'],
                'head_count' => 1,
                'ids'        => [$feemaster->id],
            ]);
        }

        return $rows;
    }

    /**
     * The payments made against one fee, one line per receipt rather than one per head.
     *
     * feeRowsFromMasters() already draws a Main Fee Head as the single charge it is. What sat
     * underneath it did not: paying one fee of 4,270 writes a fee_collections row for every sub
     * head it fills, so the student read a page of lines - 30.00, 50.00, 50.00, 40.00 - all with
     * the same date and the same reference, and the head numbers showing in the remark. One
     * payment, drawn twenty-six times, with the college's internal breakdown on display.
     *
     * A receipt is what the student actually did: a date, a reference, a method, an instalment.
     * Rows sharing all four are one payment and are added together. The money is untouched -
     * this only decides how it is read.
     *
     * Shared rather than written into the student's table, because the guardian's page and the
     * office profile draw the same fee and must not tell three different stories about it.
     */
    public function paymentRowsFor($feeCollections, array $masterIds)
    {
        $mine = collect($feeCollections)->filter(function ($c) use ($masterIds) {
            return in_array($c->fee_masters_id, $masterIds);
        });

        return $mine
            ->groupBy(function ($c) {
                return implode('|', [
                    (string) $c->date,
                    (string) $c->ref_no,
                    (string) $c->external_ref_no,
                    (string) $c->payment_method,
                    (string) $c->installment_number,
                    /* A cancelled row must never be folded in with a successful one. */
                    (string) $c->status,
                ]);
            })
            ->map(function ($rows) {
                /* The first row carries everything the line shows except the money, and every
                   row in the group agrees on all of it - that is what made them a group. */
                $first = $rows->first();

                return (object) [
                    'date'               => $first->date,
                    'ref_no'             => $first->ref_no,
                    'external_ref_no'    => $first->external_ref_no,
                    'payment_method'     => $first->payment_method,
                    'installment_number' => $first->installment_number,
                    'status'             => $first->status,
                    'verified_at'        => $first->verified_at ?? null,
                    'note'               => $first->note ?? null,
                    'paid_amount'        => $rows->sum('paid_amount'),
                    'discount'           => $rows->sum('discount'),
                    'fine'               => $rows->sum('fine'),
                    /* How many heads this one payment filled. Not shown to a student, but the
                       office profile can say "26 heads" without listing them. */
                    'head_count'         => $rows->count(),
                ];
            })
            ->sortBy('date')
            ->values();
    }

    public function activeFeeHead()
    {
        $feeHead = FeeHead::select('id', 'fee_head_title')->Active()->orderBy('fee_head_title')->pluck('fee_head_title','id')->toArray();
        return array_prepend($feeHead,'Select Fee Head',0);
    }

    /**
     * The Fee Head filter list with Main Fee Heads offered as well, as "GROUP:<id>".
     *
     * Separate from activeFeeHead() on purpose: that list feeds screens where a head id is
     * written straight into fee_masters, and a fee is not a head - it would be stored as
     * nonsense. Only screens that read money back should offer it.
     */
    public function activeFeeHeadWithGroups()
    {
        $heads = FeeHead::select('id', 'fee_head_title')->Active()
            ->orderBy('fee_head_title')->pluck('fee_head_title', 'id')->toArray();

        $groups = FeeHeadGroup::with('items')->where('status', 1)->orderBy('title')->get();
        if ($groups->isEmpty()) {
            return array_prepend($heads, 'Select Fee Head', 0);
        }

        $groupOptions = [];
        foreach ($groups as $g) {
            $groupOptions['GROUP:' . $g->id] = $g->title . ' (' . $g->items->count() . ' heads)';
        }

        /* Two labelled optgroups. A Main Fee Head dropped into the alphabetical list cannot be
           told apart from an ordinary head of a similar name, and picking the wrong one gives
           a report for one head instead of twenty-six. */
        return [
            0 => 'Select Fee Head',
            'Main Fee Head - all its sub heads together' => $groupOptions,
            'Fee Head' => $heads,
        ];
    }

    /** Group id when the picked filter is a Main Fee Head, otherwise null. */
    public function feeHeadGroupIdFromFilter($value)
    {
        $value = (string) $value;

        if (strpos($value, 'GROUP:') !== 0) {
            return null;
        }

        return (int) substr($value, 6);
    }

    /** Heading for whatever the filter picked, head or whole fee. */
    public function feeFilterTitle($value)
    {
        $groupId = $this->feeHeadGroupIdFromFilter($value);

        if ($groupId === null) {
            return $this->getFeeHeadById($value);
        }

        $group = FeeHeadGroup::find($groupId);
        return $group ? $group->title : 'Unknown Main Fee Head';
    }

    /**
     * Narrow a fee_collections query (joined as `fm`) to whatever the filter picked.
     *
     * For a fee this matches the charges that CAME FROM it, not every charge that happens to
     * sit on one of its heads - TRANSPORT can also be charged on its own, and counting that as
     * admission money would overstate the fee.
     */
    public function applyFeeHeadFilter($query, $value)
    {
        $groupId = $this->feeHeadGroupIdFromFilter($value);

        if ($groupId === null) {
            return $query->where('fm.fee_head', $value);
        }

        /* A recurring run tags the period on the end ("GROUP-3-2026-08") while a one-off does
           not, so both shapes have to match - and matching the prefix alone would let GROUP-1
           swallow GROUP-10. */
        return $query->where(function ($q) use ($groupId) {
            $q->where('fm.billing_period_key', 'GROUP-' . $groupId)
              ->orWhere('fm.billing_period_key', 'like', 'GROUP-' . $groupId . '-%');
        });
    }

    public function activePayrollHead()
    {
        $payrollHead = PayrollHead::select('id', 'title')->Active()->orderBy('title')->pluck('title','id')->toArray();
        return array_prepend($payrollHead,'Select Payroll Head',0);
    }

    public function activePaymentMethod()
    {
        $method = PaymentMethod::Active()->orderBy('id')->pluck('title','title')->toArray();
        return array_prepend($method,'','');
    }

    public function getBalanceFeeByStudentId($id)
    {
        $student = Student::where('id',$id)->first();
        $feeMaster = $student->feemaster()->sum('fee_amount');
        $feeCollection = $student->feeCollect()->get();
        $paidAmount = $feeCollection->sum('paid_amount');
        $discount = $feeCollection->sum('discount');
        $fine = $feeCollection->sum('fine');
        $balanceFee = ($feeMaster - ($paidAmount+$discount))+$fine;
       return $balanceFee;
    }

    public function dueBulkReceive($request)
    {
        $students = Student::where('id',$request->students_id)->get();
        //student filter
        $filtered  = $students->filter(function ($student) use ($request) {
            $feeMaster = $student->feeMaster()->orderBy('fee_due_date','asc')->get();
            $student->fee_amount = $feeMaster->sum('fee_amount');
            $student->paid_amount = $student->feeCollect()->sum('paid_amount');
            $student->discount = $student->feeCollect()->sum('discount');
            $student->fine = $student->feeCollect()->sum('fine');
            $student->balance = ($student->fee_amount + $student->fine) - ($student->discount + $student->paid_amount);
            $totalReceiveAmt = intval($request->receive_amount);

            if($student->balance > 0 && $totalReceiveAmt > 0){
                /*filter due using call back*/
                $receiveAmount = $totalReceiveAmt;
                foreach ($feeMaster as $fm){
                    $fee_amount = $fm->fee_amount;
                    $paid_amount = $fm->feeCollect()->sum('paid_amount');
                    $discount = $fm->feeCollect()->sum('discount');
                    $fine = $fm->feeCollect()->sum('fine');
                    $balance = ($fee_amount + $fine) - ($discount + $paid_amount);

                    if($receiveAmount > 0 && $balance > 0){
                        if($balance > $receiveAmount){
                            $collectionData = [
                                'students_id'       => $request->students_id,
                                'fee_masters_id'    => $fm->id,
                                'date'              => $request->date,
                                'paid_amount'       => $receiveAmount,
                                'payment_method'      => $request->payment_method,
                                'note'              => $request->note?'Quick Receive : '.$request->note:'Quick Receive',
                                'created_by'        => auth()->user()->id
                            ];

                            $data = FeeCollection::create($collectionData);
                            $receiveAmount = 0;
                        }else{
                            if($receiveAmount > 0 ){
                                $collectionData = [
                                    'students_id'       => $request->students_id,
                                    'fee_masters_id'    => $fm->id,
                                    'date'              => $request->date,
                                    'paid_amount'       =>$balance,
                                    'payment_method'      => $request->payment_method,
                                    'note'              => 'Quick Receive : '. $request->note,
                                    'created_by'        => auth()->user()->id
                                ];

                                $data = FeeCollection::create($collectionData);
                                $receiveAmount  = $receiveAmount  - $balance;
                            }else{

                            }

                        }
                    }
                }

            }

        });

        return back();
    }


    //send fee receive alert
    public function feeReceiveAlert($studentId, $amount)
    {
        $emailIds = [];
        $contactNumbers = [];
        $alert = AlertSetting::select('sms','email','subject','template')->where('event','=','FeeReceive')->first();
        if(!$alert) {

        }else{
            $student = Student::find($studentId);
            $today = Carbon::today()->format('Y-m-d');
            //send alert
            //Dear {{first_name}}, We would like to inform you we are successfully received {{amount}} on {{date}}. Thank you for the Deposit.
            $subject = $alert->subject;
            $message = $alert->template;
            $message = str_replace('{{first_name}}', $student->first_name, $message );
            $message = str_replace('{{amount}}', $amount, $message );
            $message = str_replace('{{date}}', $today, $message );
            $emailIds[] = $student->email;
            $contactNumbers[] = $this->getStudentMobileNumber($student->id);

            /*Now Send SMS On First Mobile Number*/
            if($alert->sms == 1){
                $contactNumbers = $this->contactFilter($contactNumbers);
                $smssuccess = $this->sendSMS($contactNumbers,$message);
            }

            /*Now Send Email With Subject*/
            if($alert->email == 1){
                $emailIds = $this->emailFilter($emailIds);
                $emailSuccess = $this->sendEmail($emailIds, $subject, $message);
            }

        }
    }


    /*Common Helper Function for this class*/
    /*public function emailFilter($emailCollections)
    {
        if(!empty($emailCollections)){
            //remove unwanted space from email address
            $emailCollections=array_map('trim',$emailCollections);
            $emailIds‍‍ = [];
            foreach($emailCollections as $email){
                //chek email id is correct or not if correct add on array other wise not
                $filterMail = filter_var($email,FILTER_VALIDATE_EMAIL);
                if($filterMail){
                    $emailIds[] = $filterMail;
                }
            }

            if(!isset($emailIds)) {
                return back()->with($this->message_warning, "No Any Email Id Found. Please, Select Your Target With Valid Email Group");
            }

            $emailIds = array_unique($emailIds);
            /*array to string separated with comma
            return $emailIds = implode(",",$emailIds);

        }else{
            return back()->with($this->message_warning, "No Any Email Id Found. Please, Select Your Target With Valid Email Group");
        }
    }*/

    /*public function contactFilter($contactNumbers){
        //The Contact Number length and filter array
        $contactNumbers =array_values((array_filter($numbers, function($v){
            return strlen($v) == 10;
        })));
        //Filter Duplicate Number get unique number
        $contactNumbers = array_unique($contactNumbers);
        //array to string comma separated number
        return $contactNumbers = implode(",",$contactNumbers);
    }*/

    public function addressVillage()
    {
        $village = Addressinfo::select('address')->get();
        if($village->count() > 0){
            $fetchAddress = array_prepend(array_unique($village->pluck('address','address')->toArray()),'','');
        }else{
            $fetchAddress = [];
        }

        return $fetchAddress;

    }


    public function getAssetsById($id)
    {
        $assets = Assets::find($id);
        if ($assets) {
            return $assets->title;
        }else{
            return "Unknown";
        }
    }
}