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

use App\Models\Web\WebSubscriber;
use Illuminate\Http\Request;
use View, AppHelper, Image, URL;
use Mail;
use Illuminate\Support\Facades\Validator;

class SubscribersController extends AdminBaseController
{
    protected $base_route = 'web.admin.subscribers';
    protected $view_path = 'web.admin.subscribers';
    protected $panel = 'Subscribers';

    protected $filter_query = [];

    public function index(Request $request)
    {

        $data = [];
        $data['rows'] = WebSubscriber::where(function ($query) use ($request) {
                if ($request->has('name')) {
                    $query->where('name', 'like', '%'.$request->name.'%');
                    $this->filter_query['name'] = $request->name;
                }

                if ($request->has('email')) {
                    $query->where('email', 'like', '%'.$request->email.'%');
                    $this->filter_query['email'] = $request->email;
                }

                if ($request->has('status')) {
                    $query->where('status', 'like', '%'.$request->status.'%');
                    $this->filter_query['status'] = $request->status;
                }
            })
            ->paginate($this->pagination_limit);

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

        return view(parent::WebsiteViewHelper('web.admin.subscribers.index'), compact('data'));

    }

    public function active(request $request, $id)
    {
        if (!$row = WebSubscriber::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->email.' Subscribe Successfully.');
        return redirect()->route($this->base_route);
    }

    public function inActive(request $request, $id)
    {
        if (!$row = WebSubscriber::find($id)) return parent::invalidRequest();

        $request->request->add(['status' => 'in-active']);

        $row->update($request->all());

        $request->session()->flash($this->message_success, $row->email.' UnSubscribe Successfully.');
        return redirect()->route($this->base_route);
    }

    public function delete(request $request, $id)
    {
        if (!$row = WebSubscriber::find($id)) return parent::invalidRequest();

        $row->delete();

        $request->session()->flash($this->message_success, $row->email.' Delete Successfully.');
        return redirect()->route($this->base_route);
    }

    public function bulkAction(Request $request)
    {
        if ($request->has('bulk_action') && in_array($request->get('bulk_action'), ['active', 'in-active', 'delete'])) {

            if ($request->has('chkIds')) {

                foreach ($request->get('chkIds') as $row_id) {

                    switch ($request->get('bulk_action')) {
                        case 'active':
                        case 'in-active':

                            $row = WebSubscriber::find($row_id);
                            if ($row) {
                                $row->status = $request->get('bulk_action') == 'active'?'active':'in-active';
                                $row->save();
                            }

                            break;
                        case 'delete':
                            $row = WebSubscriber::find($row_id);
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

    /*bulk import*/
    public function importSubscriber()
    {
        return view(parent::WebsiteViewHelper($this->view_path.'.import'));
    }

    public function handleImportSubscriber(Request $request)
    {
        //file present or not validation
        $validator = Validator::make($request->all(), [
            'file' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator);
        }

        $file = $request->file('file');
        $csvData = file_get_contents($file);
        $rows = array_map("str_getcsv", explode("\n", $csvData));
        $header = array_shift($rows);



        foreach ($rows as $row) {
            if (count($header) != count($row)) {
                continue;
            }

            $row = array_combine($header, $row);

            //Email validation
            $validator = Validator::make($row, [
                'email' => 'unique:web_subscribers,email',
            ]);

            if ($validator->fails()) {
               /* return redirect()
                    ->back()
                    ->withErrors($validator);*/
                $request->session()->flash($this->message_warning, 'Some Email Already Exist in Our Subscriber Directory / Wrong Email Address.');

            }else{
                //Subscriber import
                $customer = WebSubscriber::create([
                    "name"                  => $row['name'],
                    "email"                  => $row['email'],
                    'created_by'            => auth()->user()->id
                ]);

                $request->session()->flash($this->message_success, 'Subscriber imported Successfully.');

            }


        }

        return redirect()->route($this->base_route);
    }


}
