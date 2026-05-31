<?php

namespace App\Http\Controllers\Account\Fees;

use App\Http\Controllers\CollegeBaseController;
use App\Models\BillingAuditLog;
use App\Models\BillingSetting;
use Illuminate\Http\Request;

class BillingSettingController extends CollegeBaseController
{
    protected $base_route = 'account.fees.billing-settings';
    protected $view_path  = 'account.fees.billing-settings';
    protected $panel      = 'Billing Settings';

    public function index()
    {
        $data['setting']    = BillingSetting::instance();
        $data['audit_logs'] = BillingAuditLog::with('performer')
            ->latest()
            ->paginate(30);
        $data['view_path']  = $this->view_path;
        $data['base_route'] = $this->base_route;
        $data['panel']      = $this->panel;

        return view(parent::loadDataToView($this->view_path . '.index'), $data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'scheduler_hour'   => 'required|integer|between:0,23',
            'scheduler_minute' => 'required|integer|between:0,59',
        ]);

        $setting  = BillingSetting::instance();
        $oldState = $setting->only(['scheduler_hour', 'scheduler_minute', 'scheduler_enabled']);

        $setting->scheduler_hour    = (int) $request->scheduler_hour;
        $setting->scheduler_minute  = (int) $request->scheduler_minute;
        $setting->scheduler_enabled = $request->has('scheduler_enabled') ? 1 : 0;
        $setting->updated_by        = auth()->id();
        $setting->save();

        BillingAuditLog::create([
            'action'       => 'setting_updated',
            'entity_type'  => 'billing_setting',
            'entity_id'    => $setting->id,
            'notes'        => 'Scheduler time changed to ' . $setting->scheduler_time,
            'old_values'   => $oldState,
            'new_values'   => $setting->fresh()->only(['scheduler_hour', 'scheduler_minute', 'scheduler_enabled']),
            'performed_by' => auth()->id(),
            'ip_address'   => $request->ip(),
        ]);

        return redirect()->route($this->base_route)
            ->with('message_success', 'Billing scheduler settings saved. Auto-billing will now run daily at ' . $setting->scheduler_time . '.');
    }
}
