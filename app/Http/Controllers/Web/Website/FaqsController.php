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


use App\Models\Faq;
use App\Models\Web\WebFaq;
use Illuminate\Http\Request;

class FaqsController extends WebsiteBaseController
{
    protected $view_path = 'web.website.faq';
    public function __construct()
    {
        $this->middleware('page-status');
    }

    public function index(Request $request)
    {
        $data = [];

        $data['rows'] = WebFaq::select('id', 'question', 'answer')
            ->where(function ($query) use ($request) {

                if ($request->has('s')) {
                    $query->where('question', 'like', '%' . $request->s . '%');
                    $this->filter_query['question'] = $request->s;
                }
            })
            ->Active()
            ->latest()
            ->orderBy('rank','asc')
            ->get();
            //->paginate($this->pagination_limit);

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }
}