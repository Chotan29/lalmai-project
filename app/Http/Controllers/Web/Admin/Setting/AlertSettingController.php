<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Controllers\Web\Admin\Setting;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Models\AlertSetting;
use Illuminate\Http\Request;

class AlertSettingController extends AdminBaseController
{
    protected $base_route = 'web.admin.settings.alert';
    protected $view_path = 'web.admin.settings.alert';
    protected $panel = 'Alert Template';

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $data = [];
        $data['row'] = AlertSetting::select('id','created_by', 'last_updated_by', 'event', 'sms', 'email', 'subject', 'template',
                        'status')->get();

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

    public function add()
    {
        return view(parent::WebsiteViewHelper($this->view_path.'.add'), compact('data'));
    }

    public function store(Request $request)
    {

        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['sms' => $request->sms?$request->sms:0]);
        $request->request->add(['email' => $request->email?$request->email:0]);

        AlertSetting::create($request->all());

        $request->session()->flash($this->message_success, $this->panel. ' successfully added.');
        return redirect()->route($this->view_path);
    }


    public function edit(Request $request, $id)
    {

        $data = [];
        if (!$data['row'] = AlertSetting::find($id))
            return parent::invalidRequest();

        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path.'.edit'), compact('data'));
    }

    public function update(Request $request, $id)
    {
        if (!$row = AlertSetting::find($id)) return parent::invalidRequest();

        $request->request->add(['last_updated_by' => auth()->user()->id]);
        $request->request->add(['sms' => $request->sms?$request->sms:0]);
        $request->request->add(['email' => $request->email?$request->email:0]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel.' successfully updated.');
        return redirect()->route($this->base_route);
    }

}