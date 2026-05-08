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

use App\Models\Web\WebService;

class ServiceController extends WebsiteBaseController
{
    protected $view_path = 'web.website.services';
    public function __construct()
    {

    }

    public function index()
    {
        $this->middleware('page-status');

        $data = [];
        $data['services'] = WebService::where('status', 1)
                                    ->orderBy('rank', 'asc')
                                    ->Active()
                                    ->get();

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }
}