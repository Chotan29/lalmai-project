<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\Web\Website;

use App\Http\Requests\StaffContact\AddValidation;
use App\Mail\MailSubscribers;
use App\Mail\StaffContact;
use App\Models\ContactUsSetting;
use App\Models\GeneralSetting;
use App\Models\Staff;
use App\Models\Web\WebStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StaffController extends WebsiteBaseController
{

    protected $view_path = 'web.website.staff';

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $this->middleware('page-status');
        $data = [];
        $data['pageDetail'] = $request->instance()->query('pageStatus');

        $data['staffs'] = WebStaff::Active()
                                ->orderBy('rank')
                                ->get();

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));

    }

    public function detail(Request $request, $id)
    {
        $id = decrypt($id);
        $data['staffs'] = WebStaff::active()->find($id);
        return view(parent::WebsiteViewHelper($this->view_path.'.staff-detail'), compact('data'));
    }

    public function message(Request $request)
    {
        /*
         *  "_token" => "fmAsCEsURmtllAEohgadCoqnp23oGbSDtM75r3Lu"
              "staff" => "eyJpdiI6Im9DVHl3T0VubkpPMndZaXpjMFJzNkE9PSIsInZhbHVlIjoiRWdzRU1vOStaaHZqYnhjMzIxTXFaZz09IiwibWFjIjoiMDE1NGFiNThmYzEwZjkxMGQ0Y2EzZjY1MzE0M2ExZTJjZjBiZGExOWU0ODhi ▶"
              "staff_name" => "Uttam Raj"
              "send_to_email" => "test@test.com"
              "first_name" => "Uttam"
              "last_name" => "Raj"
              "email" => "test@test.com"
              "phone" => "9868156047"
              "message" => "dsfad"
         * */

        if($request->has('send_to_email')){
            //Mail::to($request->get('send_to_email'))->send(new StaffContact(['message_detail' => $request,'general_setting' => $general_setting,'contactUs_setting' => $contactUs_setting]));

            Mail::to($request->get('email'))->send(new StaffContact([
                'message_detail' => $request,
            ]));

            $request->session()->flash($this->message_success,'Dear '.$request->get('first_name').', Message Send Successfully. I will contact you soon.');

        }

        return back();

    }
}