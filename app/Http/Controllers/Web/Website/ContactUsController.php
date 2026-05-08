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

use App\Http\Requests\Web\ContactList\AddValidation;
use App\Http\Controllers\Controller;
use App\Models\Web\WebContactList;
use App\Models\Web\WebContactUsSetting;
use App\Rules\GoogleRecaptcha;
use Illuminate\Http\Request;
use App\Models\ContactList;

class ContactUsController extends WebsiteBaseController
{
    protected $base_route = 'web.contact_us';
    protected $view_path = 'web.website.contact_us';

    public function __construct()
    {

    }

    public function index()
    {
        $this->middleware('page-status');
        $data = [];
        $data['contactUs_setting'] = WebContactUsSetting::first();

        if(!$data['contactUs_setting'])
            return redirect()->route('web.home');

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }


    public function store(AddValidation $request)
    {

        WebContactList::create($request->all());

        $request->session()->flash('message_success',' Thank You For Your Message. We Will Contact You Soon.');
        return redirect()->route('contact-us');
    }    


}