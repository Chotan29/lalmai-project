<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
/**
 * Created by PhpStorm.
 * User: Umesh Kumar Yadav
 * Date: 02/04/2018
 * Time: 12:38 PM
 */
namespace App\Http\Controllers\Setting;
use App\Http\Controllers\CollegeBaseController;

use App\Models\EmailSetting;
use App\Models\SmsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SmsSettingController extends CollegeBaseController
{
    protected $base_route = 'setting.sms';
    protected $view_path = 'setting.sms';
    protected $panel = 'SMS Setting';

    public function __construct()
    {

    }

    public function index()
    {
        $this->syncGatewayCatalog();
        $this->cleanupDuplicateGateways();
        $data['smsSetting'] = SmsSetting::orderBy('identity')->get();
        //dd($data['smsSetting']);
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));
    }

    private function syncGatewayCatalog()
    {
        $jsonPath = base_path('database/data/sms-gateway.json');
        if (!File::exists($jsonPath)) {
            return;
        }

        $gatewayList = json_decode(File::get($jsonPath), true);
        if (!is_array($gatewayList)) {
            return;
        }

        foreach ($gatewayList as $gateway) {
            if (!isset($gateway['identity'])) {
                continue;
            }

            // sms_settings.identity is varchar(15), so normalize to DB-safe key
            $identity = mb_substr((string) $gateway['identity'], 0, 15);

            SmsSetting::firstOrCreate(
                ['identity' => $identity],
                [
                    'logo' => isset($gateway['logo']) ? $gateway['logo'] : '',
                    'link' => isset($gateway['link']) ? $gateway['link'] : '',
                    'config' => json_encode(isset($gateway['config']) ? $gateway['config'] : []),
                    'status' => isset($gateway['status']) ? $gateway['status'] : 0,
                ]
            );
        }
    }

    private function cleanupDuplicateGateways()
    {
        $duplicateIdentities = SmsSetting::select('identity')
            ->groupBy('identity')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('identity');

        foreach ($duplicateIdentities as $identity) {
            $rows = SmsSetting::where('identity', $identity)
                ->orderBy('updated_at', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            if ($rows->count() < 2) {
                continue;
            }

            // Keep most recently updated row to honor latest user action.
            $keeper = $rows->first();

            if (!$keeper->config || $keeper->config === '[]' || $keeper->config === '{}') {
                $configSource = $rows->first(function ($row) {
                    return !empty($row->config) && $row->config !== '[]' && $row->config !== '{}';
                });

                if ($configSource) {
                    $keeper->config = $configSource->config;
                }
            }

            $keeper->save();

            SmsSetting::where('identity', $identity)
                ->where('id', '!=', $keeper->id)
                ->delete();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!$paymentSetting = SmsSetting::find($id)) return parent::invalidRequest();

        $existingConfig = json_decode((string) $paymentSetting->config, true);
        if (!is_array($existingConfig)) {
            $existingConfig = [];
        }

        $incomingConfig = $request->except('_token');
        if (!is_array($incomingConfig)) {
            $incomingConfig = [];
        }

        // Preserve existing keys when partial payloads are posted.
        $config = json_encode(array_merge($existingConfig, $incomingConfig));
        $paymentSetting->update([
            'config' => $config
        ]);

        $request->session()->flash($this->message_success, $this->panel. ' successfully updated.');
        return redirect()->route($this->base_route);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id)
    {

    }


    public function active(request $request, $id)
    {
        if (!$row = SmsSetting::find($id)) return parent::invalidRequest();

        // Keep only one active gateway at a time.
        SmsSetting::whereNotIn('id', [$id])->update(['status' => 0]);
        $row->update(['status' => 1]);

        $request->session()->flash($this->message_success, $row->identity.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        if (!$row = SmsSetting::find($id)) return parent::invalidRequest();

        // Deactivate all duplicate rows with same identity to avoid ghost active rows.
        SmsSetting::where('identity', $row->identity)->update(['status' => 0]);

        $request->session()->flash($this->message_success, $row->identity.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }

}