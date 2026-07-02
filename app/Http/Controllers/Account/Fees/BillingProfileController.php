<?php

namespace App\Http\Controllers\Account\Fees;

use App\Http\Controllers\CollegeBaseController;
use App\Models\BillingProfile;
use App\Models\BillingProfileItem;
use App\Models\Faculty;
use App\Models\FeeHead;
use App\Models\Semester;
use App\Models\StudentBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillingProfileController extends CollegeBaseController
{
    protected $base_route = 'account.fees.billing-profile';
    protected $view_path  = 'account.fees.billing-profile';
    protected $panel      = 'Billing Profile';

    // -------------------------------------------------------
    // LIST
    // -------------------------------------------------------

    public function index(Request $request)
    {
        $data['billing_profiles'] = BillingProfile::with(['profileItems', 'faculty', 'semester', 'batch'])
            ->orderByDesc('id')
            ->paginate(env('PAGINATION_LIMIT', 50));

        $data['view_path']  = $this->view_path;
        $data['base_route'] = $this->base_route;
        $data['panel']      = $this->panel;

        return view(parent::loadDataToView($this->view_path . '.index'), $data);
    }

    // -------------------------------------------------------
    // CREATE FORM
    // -------------------------------------------------------

    public function create()
    {
        $data['faculties']   = Faculty::where('status', 1)->orderBy('faculty')->get();
        $data['semesters']   = Semester::where('status', 1)->orderBy('semester')->get();
        $data['batches']     = StudentBatch::orderBy('title')->get();
        $data['fee_heads']   = FeeHead::where('status', 1)->orderBy('fee_head_title')->get();
        $data['view_path']   = $this->view_path;
        $data['base_route']  = $this->base_route;
        $data['panel']       = $this->panel;
        $data['months']      = $this->monthList();
        $data['cycles']      = $this->cycleList();

        return view(parent::loadDataToView($this->view_path . '.add'), $data);
    }

    // -------------------------------------------------------
    // STORE
    // -------------------------------------------------------

    public function store(Request $request)
    {
        $request->validate([
            'profile_name'   => 'required|string|max:200',
            'scope_type'     => 'required|in:all,faculty,semester,batch',
            'billing_cycle'  => 'required|in:monthly,quarterly,half_yearly,yearly,one_time',
            'fee_head_id'    => 'required|array|min:1',
            'fee_head_id.*'  => 'required|exists:fee_heads,id',
        ]);

        // Build billing_months array
        $billingMonths = $this->parseBillingMonths($request);

        // Validate billing_day requirement
        if ($request->billing_cycle !== 'one_time') {
            $request->validate([
                'billing_day' => 'required|integer|min:1|max:28',
            ]);
        } else {
            $request->validate([
                'one_time_date' => 'required|date',
            ]);
        }

        $profile = BillingProfile::create([
            'created_by'           => auth()->id(),
            'profile_name'         => $request->profile_name,
            'description'          => $request->description,
            'scope_type'           => $request->scope_type,
            'faculty_id'           => $request->scope_type === 'faculty'   ? $request->faculty_id   : null,
            'semester_id'          => $request->scope_type === 'semester'  ? $request->semester_id  : null,
            'batch_id'             => $request->scope_type === 'batch'     ? $request->batch_id     : null,
            'only_active_students' => (bool) $request->input('only_active_students', 1),
            'only_regular_status'  => (bool) $request->input('only_regular_status', 1),
            'billing_cycle'        => $request->billing_cycle,
            'billing_day'          => $request->billing_cycle !== 'one_time' ? $request->billing_day : null,
            'billing_months'       => $billingMonths,
            'one_time_date'        => $request->billing_cycle === 'one_time' ? $request->one_time_date : null,
            'due_days'             => (int) ($request->due_days ?? 15),
            'fine_type'            => $request->fine_type ?? 'none',
            'fine_amount'          => (float) ($request->fine_amount ?? 0),
            'fine_grace_days'      => (int) ($request->fine_grace_days ?? 0),
            'max_fine'             => $request->filled('max_fine') ? (float) $request->max_fine : null,
            'installment_count'    => (int) ($request->installment_count ?? 1),
            'installment_splits'   => $this->parseInstallmentSplits($request),
            'sms_on_generation'    => (bool) $request->input('sms_on_generation', 0),
            'alert_event_key'      => $request->filled('alert_event_key') ? $request->alert_event_key : 'BillingGenerated',
            'status'               => 1,
        ]);

        // Save profile items
        $this->saveProfileItems($profile, $request);

        return redirect()->route('account.fees.billing-profile')
            ->with('message_success', 'Billing profile "' . $profile->profile_name . '" created successfully.');
    }

    // -------------------------------------------------------
    // EDIT FORM
    // -------------------------------------------------------

    public function edit(int $id)
    {
        $data['profile']     = BillingProfile::with('profileItems.feeHead')->findOrFail($id);
        $data['faculties']   = Faculty::where('status', 1)->orderBy('faculty')->get();
        $data['semesters']   = Semester::where('status', 1)->orderBy('semester')->get();
        $data['batches']     = StudentBatch::orderBy('title')->get();
        $data['fee_heads']   = FeeHead::where('status', 1)->orderBy('fee_head_title')->get();
        $data['view_path']   = $this->view_path;
        $data['base_route']  = $this->base_route;
        $data['panel']       = $this->panel;
        $data['months']      = $this->monthList();
        $data['cycles']      = $this->cycleList();

        return view(parent::loadDataToView($this->view_path . '.edit'), $data);
    }

    // -------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------

    public function update(Request $request, int $id)
    {
        $profile = BillingProfile::findOrFail($id);

        $request->validate([
            'profile_name'  => 'required|string|max:200',
            'scope_type'    => 'required|in:all,faculty,semester,batch',
            'billing_cycle' => 'required|in:monthly,quarterly,half_yearly,yearly,one_time',
            'fee_head_id'   => 'required|array|min:1',
            'fee_head_id.*' => 'required|exists:fee_heads,id',
        ]);

        $billingMonths = $this->parseBillingMonths($request);

        $profile->update([
            'updated_by'           => auth()->id(),
            'profile_name'         => $request->profile_name,
            'description'          => $request->description,
            'scope_type'           => $request->scope_type,
            'faculty_id'           => $request->scope_type === 'faculty'   ? $request->faculty_id   : null,
            'semester_id'          => $request->scope_type === 'semester'  ? $request->semester_id  : null,
            'batch_id'             => $request->scope_type === 'batch'     ? $request->batch_id     : null,
            'only_active_students' => (bool) $request->input('only_active_students', 1),
            'only_regular_status'  => (bool) $request->input('only_regular_status', 1),
            'billing_cycle'        => $request->billing_cycle,
            'billing_day'          => $request->billing_cycle !== 'one_time' ? $request->billing_day : null,
            'billing_months'       => $billingMonths,
            'one_time_date'        => $request->billing_cycle === 'one_time' ? $request->one_time_date : null,
            'due_days'             => (int) ($request->due_days ?? 15),
            'fine_type'            => $request->fine_type ?? 'none',
            'fine_amount'          => (float) ($request->fine_amount ?? 0),
            'fine_grace_days'      => (int) ($request->fine_grace_days ?? 0),
            'max_fine'             => $request->filled('max_fine') ? (float) $request->max_fine : null,
            'installment_count'    => (int) ($request->installment_count ?? 1),
            'installment_splits'   => $this->parseInstallmentSplits($request),
            'sms_on_generation'    => (bool) $request->input('sms_on_generation', 0),
            'alert_event_key'      => $request->filled('alert_event_key') ? $request->alert_event_key : 'BillingGenerated',
        ]);

        // Replace profile items
        $profile->profileItems()->delete();
        $this->saveProfileItems($profile, $request);

        return redirect()->route('account.fees.billing-profile')
            ->with('message_success', 'Billing profile updated successfully.');
    }

    // -------------------------------------------------------
    // ACTIVATE / DEACTIVATE
    // -------------------------------------------------------

    public function active(int $id)
    {
        BillingProfile::findOrFail($id)->update(['status' => 1]);
        return redirect()->back()->with('message_success', 'Billing profile activated.');
    }

    public function inActive(int $id)
    {
        BillingProfile::findOrFail($id)->update(['status' => 0]);
        return redirect()->back()->with('message_warning', 'Billing profile deactivated.');
    }

    // -------------------------------------------------------
    // DELETE
    // -------------------------------------------------------

    public function delete(int $id)
    {
        $profile = BillingProfile::findOrFail($id);
        if ($profile->runs()->exists()) {
            return redirect()->back()->with('message_warning', 'Cannot delete profile with existing billing runs. Deactivate it instead.');
        }
        $profile->delete();
        return redirect()->route('account.fees.billing-profile')
            ->with('message_success', 'Billing profile deleted.');
    }

    // -------------------------------------------------------
    // AJAX: Get fee head amount (for dynamic form row)
    // -------------------------------------------------------

    public function getFeeHeadAmount(Request $request)
    {
        $feeHead = FeeHead::find($request->fee_head_id);
        if (!$feeHead) {
            return response()->json(['amount' => 0]);
        }
        return response()->json(['amount' => $feeHead->fee_head_amount, 'title' => $feeHead->fee_head_title]);
    }

    // -------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------

    private function saveProfileItems(BillingProfile $profile, Request $request): void
    {
        $feeHeadIds = $request->fee_head_id;
        $amounts    = $request->amount_override ?? [];
        $optionals  = $request->is_optional ?? [];

        foreach ($feeHeadIds as $i => $feeHeadId) {
            if (!$feeHeadId) {
                continue;
            }
            $override = isset($amounts[$i]) && $amounts[$i] !== '' ? (float) $amounts[$i] : null;
            BillingProfileItem::create([
                'billing_profile_id' => $profile->id,
                'fee_head_id'        => $feeHeadId,
                'amount_override'    => $override,
                'is_optional'        => isset($optionals[$i]) ? 1 : 0,
                'sort_order'         => $i,
            ]);
        }
    }

    private function parseBillingMonths(Request $request): ?array
    {
        switch ($request->billing_cycle) {
            case 'monthly':
                return null;
            case 'quarterly':
                return [1, 4, 7, 10];
            case 'half_yearly':
            case 'yearly':
                return array_values(array_filter(array_map('intval', (array) $request->billing_months)));
            case 'one_time':
            default:
                return null;
        }
    }

    private function parseInstallmentSplits(Request $request): ?array
    {
        if ((int) $request->installment_count <= 1) {
            return null;
        }
        $splits = array_filter(array_map('intval', (array) $request->installment_splits));
        return count($splits) ? array_values($splits) : null;
    }

    private function monthList(): array
    {
        return [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];
    }

    private function cycleList(): array
    {
        return [
            'monthly'     => 'Monthly',
            'quarterly'   => 'Quarterly (Jan, Apr, Jul, Oct)',
            'half_yearly' => 'Half-Yearly',
            'yearly'      => 'Yearly',
            'one_time'    => 'One-Time',
        ];
    }
}
