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
use App\Models\Web\WebContactList;


use App\Http\Requests\Web\ContactUsSetting\AddValidation;
use App\Http\Requests\Web\ContactUsSetting\EditValidation;
use App\Models\ContactUsSetting;
use App\Models\ContactList;
use Illuminate\Http\Request;
use View, AppHelper, Image, URL;

class ContactUsController extends AdminBaseController
{
    protected $base_route = 'web.admin.contact-us';
    protected $view_path = 'web.admin.contact-us';
    protected $panel = 'Contact Us Message';

    protected $folder_path;
    protected $folder_name = 'contact_us_settings';
    protected $filter_query = [];

    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'web'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function contactList(Request $request)
    {
        $data = [];
        $data['rows'] = WebContactList::where(function ($query) use ($request) {
                                if ($request->has('name')) {
                                    $query->where('name', 'like', '%' . $request->name . '%');
                                    $this->filter_query['name'] = $request->name;
                                }

                                if ($request->has('email')) {
                                    $query->where('email', 'like', '%' . $request->email . '%');
                                    $this->filter_query['email'] = $request->email;
                                }

                                if ($request->has('address')) {
                                    $query->where('address', 'like', '%' . $request->address . '%');
                                    $this->filter_query['address'] = $request->address;
                                }

                                if ($request->has('phone')) {
                                    $query->where('phone', 'like', '%' . $request->phone . '%');
                                    $this->filter_query['phone'] = $request->phone;
                                }

                                if ($request->has('message')) {
                                    $query->where('message', 'like', '%' . $request->message . '%');
                                    $this->filter_query['message'] = $request->message;
                                }

                                if ($request->has('description')) {
                                    $query->where('description', 'like', '%'.$request->description.'%');
                                    $this->filter_query['description'] = $request->description;
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

                                if ($request->has('view_status')) {
                                    $query->where('view_status', $request->get('view_status'));
                                    $this->filter_query['view_status'] = $request->get('view_status');
                                }

                            })
                        ->orderBy('created_at', 'desc')
                        ->paginate($this->pagination_limit);

        $data['url'] = URL::current();
        $data['filter_query'] = $this->filter_query;

      return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

     public function viewMessage(Request $request, $id)
    {
        $id = decrypt($id);
        $data = [];
        $data['row'] = WebContactList::find($id);
        $request->request->add(['view_status' => 1]);
        $data['row']->update($request->all());
      return view(parent::WebsiteViewHelper($this->view_path.'.view'), compact('data'));
    }

    public function deleteMessage(Request $request, $id)
    {
        $id = decrypt($id);
        if (!$row = WebContactList::find($id)) return parent::invalidRequest();
        $row->delete();
        
        return redirect()->route($this->base_route)->with('message_success', 'Contact Us Message Delete Successfully');
    }

}

