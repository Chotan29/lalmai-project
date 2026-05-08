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

use App\Models\Web\WebPricing;

class PricingController extends WebsiteBaseController
{
    protected $view_path = 'web.website.pricing';

    public function __construct()
    {
        $this->middleware('page-status');
    }

    public function index()
    {
        $data = [];
        //'title','old_price','new_price', 'per_term', 'description', 'image','tag','rank', 'status'

        $data['rows'] = WebPricing::Active()
                        ->orderBy('rank','asc')
                        ->get();

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }
}