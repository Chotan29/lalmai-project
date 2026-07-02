<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Controllers\Web\Admin;

use Illuminate\Http\Request;
use View, AppHelper, Image, URL;

class HelpController extends AdminBaseController
{
    
     protected $base_route = 'web.admin.help';
    protected $view_path = 'web.admin.help';
    protected $panel = 'CMS User Help';
    protected $folder_path;
    protected $filter_query = [];

    public function index(Request $request)
    {
        $data = [];
        return view(parent::WebsiteViewHelper($this->view_path.'.index'));
    }

    
}