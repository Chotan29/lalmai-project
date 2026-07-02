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


use App\Models\Client;

class ClientController extends WebsiteBaseController
{

    public function __construct()
    {

    }

    public function index()
    {
        $this->middleware('page-status');

        $data = [];
        $data['client'] = Client::where('status', 1)
                                    ->orderBy('rank', 'asc')
                                    ->Active()
                                    ->get();

        return view(parent::WebsiteViewHelper('website.client.index'), compact('data'));
    }
}