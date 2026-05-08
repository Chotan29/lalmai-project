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
use App\Models\Web\WebFaq;


use App\Http\Requests\Web\Faq\AddValidation;
use App\Http\Requests\Web\Faq\EditValidation;
use App\Models\Faq;
use Illuminate\Http\Request;
use View, AppHelper, URL;

class FaqController extends AdminBaseController
{
    protected $base_route = 'web.admin.faq';
    protected $view_path = 'web.admin.faq';
    protected $panel = 'Faq';
    protected $filter_query = [];

    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $data = [];
        $data['rows'] = WebFaq::select('id', 'question', 'answer', 'rank',  'status')
            ->where(function ($query) use ($request) {

                if ($request->has('question')) {
                    $query->where('question', 'like', '%' . $request->question . '%');
                    $this->filter_query['question'] = $request->question;
                }

                if ($request->has('answer')) {
                    $query->where('answer', 'like', '%' . $request->answer . '%');
                    $this->filter_query['answer'] = $request->answer;
                }

                if ($request->has('create_start_date') && $request->has('create_end_date')) {
                    $query->whereBetween('created_at', [$request->get('create_start_date'), $request->get('create_end_date')]);
                    $this->filter_query['create_start_date'] = $request->get('create_start_date');
                    $this->filter_query['create_end_date'] = $request->get('create_end_date');
                }
                elseif ($request->has('create_start_date')) {
                    $query->where('created_at', '>=', $request->get('create_start_date'));
                    $this->filter_query['create_start_date'] = $request->get('create_start_date');
                }
                elseif($request->has('create_end_date')) {
                    $query->where('created_at', '<=', $request->get('create_end_date'));
                    $this->filter_query['create_end_date'] = $request->get('create_end_date');
                }

                if ($request->has('update_start_date') && $request->has('update_end_date')) {
                    $query->whereBetween('updated_at', [$request->get('update_start_date'), $request->get('update_end_date')]);
                    $this->filter_query['update_start_date'] = $request->get('update_start_date');
                    $this->filter_query['update_end_date'] = $request->get('update_end_date');
                }
                elseif ($request->has('update_start_date')) {
                    $query->where('updated_at', '>=', $request->get('update_start_date'));
                    $this->filter_query['update_start_date'] = $request->get('update_start_date');
                }
                elseif($request->has('update_end_date')) {
                    $query->where('updated_at', '<=', $request->get('update_end_date'));
                    $this->filter_query['update_end_date'] = $request->get('update_end_date');
                }

                if ($request->has('status')) {
                    $query->where('status', $request->status == 'active'?1:0);
                    $this->filter_query['status'] = $request->get('status');
                }

            })
            ->orderBy('rank','asc')
            ->paginate($this->pagination_limit);


        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::WebsiteViewHelper($this->view_path . '.index'), compact('data'));
    }

    public function add()
    {
        $data = [];
        $data['blank_ins'] = new WebFaq();
        if ($latest = WebFaq::orderBy('id', 'DESC')->first())
            $rank = $latest->rank + 10;
        else
            $rank = 10;

        $data['blank_ins']->status = 'active';
        $data['blank_ins']->rank = $rank;

        return view(parent::WebsiteViewHelper($this->view_path . '.add'), compact('data'));
    }

    public function store(AddValidation $request)
    {
        $request->request->add(['created_by' => auth()->user()->id]);

        WebFaq::create($request->all());

        $request->session()->flash($this->message_success, $this->panel . ' successfully added.');
        return redirect()->route($this->base_route);
    }

    public function edit(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        if (!$data['row'] = WebFaq::find($id))
            return parent::invalidRequest();

        $data['base_route'] = $this->base_route;
        return view(parent::WebsiteViewHelper($this->view_path . '.edit'), compact('data'));
    }

    public function view($id)
    {
        $id = decrypt($id);
        $data = [];
        $data['row'] = WebFaq::find($id);
        $data['base_route'] = $this->base_route;        
        return view(parent::WebsiteViewHelper($this->view_path.'.view'), compact('data'));
    }

    public function update(EditValidation $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebFaq::find($id)) return parent::invalidRequest();

        $request->request->add(['last_updated_by' => auth()->user()->id]);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $this->panel . ' successfully updated.');
        return redirect()->route($this->base_route);
    }

    public function delete(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebFaq::find($id)) return parent::invalidRequest();

        $row->delete();

        $request->session()->flash($this->message_success, $this->panel . ' successfully deleted.');
        return redirect()->route($this->base_route);

    }

    public function active(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebFaq::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebFaq::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->reg_no.' '.$this->panel.' In-Active Successfully.');
        return redirect()->route($this->base_route);
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {

                foreach ($request->get('chkIds') as $row_id) {
                    $row_id = decrypt($row_id);
                    $row = WebFaq::find($row_id);
                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active' ? 'active' : 'in-active';
                                $row->save();
                            }

                            break;
                        case 'delete':
                            $row->delete();
                            break;
                    }

                }

                if ($request->get('bulk_action') == 'active' || $request->get('bulk_action') == 'in-active')
                    $request->session()->flash($this->message_success, 'Bulk Action Successful.');
                else
                    $request->session()->flash($this->message_success, $this->panel.' '.ucfirst($request->get('bulk_action')).' successfully.');

                return redirect()->route($this->base_route);

            } else {
                $request->session()->flash($this->message_warning, 'Please, check at least one '.$this->panel);
                return redirect()->route($this->base_route);
            }

        } else return parent::invalidRequest();


    }
}