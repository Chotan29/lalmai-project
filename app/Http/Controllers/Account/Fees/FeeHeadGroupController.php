<?php

namespace App\Http\Controllers\Account\Fees;

use App\Http\Controllers\CollegeBaseController;
use App\Http\Requests\Account\FeeHeadGroup\AddValidation;
use App\Http\Requests\Account\FeeHeadGroup\EditValidation;
use App\Models\FeeHead;
use App\Models\FeeHeadGroup;
use App\Models\FeeHeadGroupItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Main Fee Head - one fee as the student sees it, made of the sub heads the accounts need.
 *
 * A student pays "ভর্তি ফি ২০২৫-২৬, ৭,৪০০" and sees nothing else. The office sees 26 sub heads:
 * 23 college heads adding to 4,600 and 3 department heads adding to 2,800. Nothing here charges
 * anybody - it only records how a fee is composed. Money is still written against ordinary
 * fee_heads rows when a payment happens, so every existing collection screen and report keeps
 * working untouched.
 *
 * Two things are enforced here rather than left to the payment code:
 *
 *   The sub heads must add up to the total, exactly. A 7,400 fee whose heads come to 7,300
 *   cannot be saved. That is what keeps the question of proportional shares and rounding out of
 *   the moment money actually arrives.
 *
 *   The order of the sub heads is meaningful. Payment fills them in this order, so the college
 *   heads sit above the department heads: a student who pays only the college's 4,600 leaves the
 *   department heads at zero instead of part-filling them in whatever order came back.
 *
 * Nothing is ever deleted. A sub head taken out of a fee is switched off, so the record of what
 * the fee used to contain survives; a fee that has been collected against is locked outright.
 */
class FeeHeadGroupController extends CollegeBaseController
{
    protected $base_route = 'account.fees.fee-head-group';
    protected $view_path = 'account.fees.fee-head-group';
    protected $panel = 'Main Fee Head';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $data = [];
        $data['fee_head_groups'] = FeeHeadGroup::with('items.feeHead')->orderBy('id', 'desc')->get();
        $data['fee_heads'] = FeeHead::Active()->orderBy('fee_head_title', 'asc')->get();

        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        if (($message = $this->amountsBalance($request)) !== true) {
            $request->session()->flash($this->message_warning, $message);
            return redirect()->back()->withInput();
        }

        DB::transaction(function () use ($request) {
            $request->request->add(['created_by' => auth()->user()->id]);

            $row = FeeHeadGroup::create($request->all());

            $this->saveSubHeads($request, $row);
        });

        $request->session()->flash($this->message_success, $this->panel.' Created Successfully.');
        return redirect()->route($this->base_route);
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = FeeHeadGroup::with('items.feeHead')->find($id))
            return parent::invalidRequest();

        $data['fee_head_groups'] = FeeHeadGroup::with('items.feeHead')->orderBy('id', 'desc')->get();
        $data['fee_heads'] = FeeHead::Active()->orderBy('fee_head_title', 'asc')->get();

        if ($data['row']->is_locked) {
            $request->session()->flash($this->message_info,
                'Fees have already been collected against this '.$this->panel.'. It can be viewed but not changed.');
        }

        $data['base_route'] = $this->base_route;
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$row = FeeHeadGroup::find($id)) return parent::invalidRequest();

        /* Changing a fee that has already taken money would rewrite what students paid for. */
        if ($row->is_locked) {
            $request->session()->flash($this->message_warning,
                'Fees have already been collected against this '.$this->panel.'. Please create a new one instead.');
            return redirect()->route($this->base_route);
        }

        if (($message = $this->amountsBalance($request)) !== true) {
            $request->session()->flash($this->message_warning, $message);
            return redirect()->back()->withInput();
        }

        DB::transaction(function () use ($request, $row) {
            $request->request->add(['last_updated_by' => auth()->user()->id]);

            $row->update($request->all());

            $this->saveSubHeads($request, $row);
        });

        $request->session()->flash($this->message_success, $this->panel.' Updated Successfully.');
        return redirect()->route($this->base_route);
    }

    public function active(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = FeeHeadGroup::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = FeeHeadGroup::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }

    /**
     * A sub head the office needs but which is not on the list yet.
     *
     * Building a fee of twenty-six heads used to stop dead the moment one was missing: leave the
     * page for Fees Head, create it, come back, and start the fee again from an empty form. This
     * creates the same ordinary fee_heads row without leaving, and hands it back so the browser
     * can drop it into every sub head dropdown at once.
     *
     * Collected By is asked for and not defaulted. It decides whether the money is counted as
     * the college's or the department's, and a head filed on the wrong side is a reconciliation
     * that will not add up months later - by which time nobody remembers this screen.
     *
     * The route carries fees-head-add, the same permission the Fees Head screen uses, so this is
     * not a way round it.
     */
    public function storeSubHead(Request $request)
    {
        $title = trim((string) $request->get('fee_head_title'));
        $collectedBy = trim((string) $request->get('collected_by'));
        $amount = $request->get('fee_head_amount');

        $fail = function ($field, $message) {
            return response()->json(['ok' => false, 'field' => $field, 'message' => $message], 422);
        };

        if ($title === '') {
            return $fail('fee_head_title', 'Please type the sub head name.');
        }

        if (mb_strlen($title) > 100) {
            return $fail('fee_head_title', 'The name cannot be longer than 100 characters.');
        }

        if (!in_array($collectedBy, ['college', 'department'], true)) {
            return $fail('collected_by', 'Please choose whether the college or the department collects this.');
        }

        /*The column is unique, so a duplicate would be a database error rather than something the
          office could read. Told plainly, and the existing head handed back so the row can simply
          be pointed at it - which is what the office wanted anyway.*/
        $existing = FeeHead::whereRaw('LOWER(TRIM(fee_head_title)) = ?', [mb_strtolower($title)])->first();

        if ($existing) {
            return response()->json([
                'ok' => false,
                'field' => 'fee_head_title',
                'message' => 'That sub head already exists' . ($existing->status ? '' : ' but is switched off') . '.',
                'existing' => [
                    'id' => $existing->id,
                    'title' => $existing->fee_head_title,
                    'amount' => (int) $existing->fee_head_amount,
                    'collected_by' => $existing->collected_by,
                    'active' => (bool) $existing->status,
                ],
            ], 409);
        }

        $head = FeeHead::create([
            'created_by' => auth()->user()->id,
            'fee_head_title' => $title,
            'fee_head_amount' => (int) $amount,
            'collected_by' => $collectedBy,
            'status' => 1,
        ]);

        return response()->json([
            'ok' => true,
            'head' => [
                'id' => $head->id,
                'title' => $head->fee_head_title,
                'amount' => (int) $head->fee_head_amount,
                'collected_by' => $head->collected_by,
            ],
        ]);
    }

    /**
     * Next session's copy: same sub heads, same amounts, switched off until the office has
     * checked the new figures. This is how amounts change year to year without touching a fee
     * students have already paid against.
     */
    public function duplicate(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = FeeHeadGroup::with('items')->find($id)) return parent::invalidRequest();

        $copy = null;

        DB::transaction(function () use ($row, &$copy) {
            $copy = FeeHeadGroup::create([
                'created_by'   => auth()->user()->id,
                'title'        => $this->copyTitle($row->title),
                'session'      => $row->session,
                'description'  => $row->description,
                'total_amount' => $row->total_amount,
                'is_locked'    => 0,
                'status'       => 'in-active',
            ]);

            foreach ($row->items as $item) {
                FeeHeadGroupItem::create([
                    'created_by'        => auth()->user()->id,
                    'fee_head_group_id' => $copy->id,
                    'fee_head_id'       => $item->fee_head_id,
                    'amount'            => $item->amount,
                    'sort_order'        => $item->sort_order,
                    'status'            => 'active',
                ]);
            }
        });

        $request->session()->flash($this->message_success,
            $this->panel.' Copied Successfully. It stays In-Active until you check the amounts.');
        return redirect()->route($this->base_route.'.edit', encrypt($copy->id));
    }

    /* -------------------------------------------------------------------- */

    /**
     * Money is compared in paisa, never as a float. Two amounts that read the same on screen can
     * differ in the last bit as doubles, and a fee that refuses to save for no visible reason is
     * worse than no check at all.
     */
    private function toPaisa($value)
    {
        return (int) round(((float) $value) * 100);
    }

    /**
     * @return true|string  true, or the sentence explaining what does not add up
     */
    private function amountsBalance(Request $request)
    {
        $heads = array_filter((array) $request->get('fee_head_id'), function ($value) {
            return $value !== null && $value !== '';
        });

        if (count($heads) !== count(array_unique($heads))) {
            return 'The same Sub Head has been added twice. Each head may appear only once.';
        }

        $amounts = (array) $request->get('amount');
        $total = $this->toPaisa($request->get('total_amount'));
        $sum = 0;

        foreach ((array) $request->get('fee_head_id') as $key => $feeHeadId) {
            if ($feeHeadId === null || $feeHeadId === '') continue;
            $sum += $this->toPaisa(isset($amounts[$key]) ? $amounts[$key] : 0);
        }

        if ($sum !== $total) {
            return 'Sub Head total is '.number_format($sum / 100, 2)
                .' but the Main Fee Head is '.number_format($total / 100, 2)
                .' - a difference of '.number_format(abs($total - $sum) / 100, 2)
                .'. Both must match exactly.';
        }

        return true;
    }

    /**
     * Write the sub heads without destroying anything.
     *
     * A head still on the form is updated in place; a head taken off it is switched off, never
     * removed, so the fee's earlier make-up can still be read back afterwards.
     */
    private function saveSubHeads(Request $request, FeeHeadGroup $row)
    {
        $amounts = (array) $request->get('amount');
        $keptIds = [];
        $sortOrder = 0;

        foreach ((array) $request->get('fee_head_id') as $key => $feeHeadId) {
            if ($feeHeadId === null || $feeHeadId === '') continue;

            $item = FeeHeadGroupItem::where('fee_head_group_id', $row->id)
                ->where('fee_head_id', $feeHeadId)
                ->first();

            $values = [
                'amount' => $this->toPaisa(isset($amounts[$key]) ? $amounts[$key] : 0) / 100,
                /* Position on the form is the order money fills these heads in, which is why the
                   rows can be dragged rather than being sorted by name. */
                'sort_order' => $sortOrder++,
                'status' => 'active',
            ];

            if ($item) {
                $item->update($values + ['last_updated_by' => auth()->user()->id]);
            } else {
                $item = FeeHeadGroupItem::create($values + [
                    'created_by' => auth()->user()->id,
                    'fee_head_group_id' => $row->id,
                    'fee_head_id' => $feeHeadId,
                ]);
            }

            $keptIds[] = $item->id;
        }

        FeeHeadGroupItem::where('fee_head_group_id', $row->id)
            ->whereNotIn('id', $keptIds)
            ->update(['status' => 0, 'last_updated_by' => auth()->user()->id]);
    }

    private function copyTitle($title)
    {
        $copy = $title.' - Copy';
        $serial = 2;

        while (FeeHeadGroup::where('title', $copy)->exists()) {
            $copy = $title.' - Copy '.$serial++;
        }

        return $copy;
    }
}
