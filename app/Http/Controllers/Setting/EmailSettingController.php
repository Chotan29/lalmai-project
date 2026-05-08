<?php
namespace App\Http\Controllers\Setting;

use App\Http\Controllers\CollegeBaseController;
use App\Models\EmailSetting;
use Illuminate\Http\Request;
use App\Traits\EnvironmentScope;

class EmailSettingController extends CollegeBaseController
{
    use EnvironmentScope;
    protected $base_route = 'setting.email';
    protected $view_path = 'setting.email';
    protected $panel = 'Email Setting';

    public function index()
    {
        $data = [];
        $data['row'] = EmailSetting::select('id', 'driver', 'host', 'port', 'user_name', 'password', 'encryption', 'status')->first();
        $isEdit = $data['row'] ? true : false;

        return view(parent::loadDataToView($this->view_path.'.add-edit'), compact('data', 'isEdit'));
    }

    public function store(Request $request)
    {
        // Validation can be added here
        $request->validate([
            'driver' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|numeric',
            'user_name' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'encryption' => 'required|string|max:255',
        ]);

        if (EmailSetting::count() > 0) {
            return redirect()->route($this->base_route)->withErrors('Email setting already exists. Please edit instead.');
        }

        $request->request->add(['created_by' => auth()->id()]);

        $row = EmailSetting::create($request->only('driver', 'host', 'port', 'user_name', 'password', 'encryption', 'created_by'));

        $this->updateEnv($request);

        return redirect()->route($this->base_route)->with($this->message_success, $this->panel.' successfully added.');
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);

        // Validation can be added here as well
        $request->validate([
            'driver' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|numeric',
            'user_name' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'encryption' => 'required|string|max:255',
        ]);

        $row = EmailSetting::findOrFail($id);

        $row->update($request->only('driver', 'host', 'port', 'user_name', 'password', 'encryption'));

        $this->updateEnv($request);

        return redirect()->route($this->base_route)->with($this->message_success, $this->panel.' successfully updated.');
    }

    // AJAX status toggle
    public function statusChange(Request $request)
    {
        $row = EmailSetting::find($request->id);
        if (!$row) {
            return response()->json(['error' => 'Invalid setting ID'], 404);
        }

        $row->status = $request->status;
        $row->save();

        return response()->json(['success' => true]);
    }

    // Update .env settings helper
    protected function updateEnv(Request $request)
    {
        $this->setEnv('MAIL_DRIVER', $request->driver);
        $this->setEnv('MAIL_HOST', $request->host);
        $this->setEnv('MAIL_PORT', $request->port);
        $this->setEnv('MAIL_USERNAME', $request->user_name);
        $this->setEnv('MAIL_PASSWORD', $request->password);
        $this->setEnv('MAIL_ENCRYPTION', $request->encryption);
    }
}
