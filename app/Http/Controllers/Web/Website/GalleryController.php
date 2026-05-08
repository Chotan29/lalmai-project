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

use App\Models\Web\WebGallery;
use Illuminate\Http\Request;

class GalleryController extends WebsiteBaseController
{
    protected $folder_name = 'gallery';
    protected $view_path = 'web.website.gallery';
    public function __construct()
    {
        $this->folder_path = public_path().DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$this->folder_name.DIRECTORY_SEPARATOR;
    }

    public function index(Request $request)
    {
        $this->middleware('page-status');

        $data['rows'] = WebGallery::Active()
                        ->where(function ($query) use ($request) {
                            if ($request->has('s')) {
                                $query->where('title', 'like', '%'.$request->s.'%');
                                $this->filter_query['title'] = $request->s;
                            }
                        })
                        ->orderBy('rank', 'asc')
                        ->paginate($this->pagination_limit);

        return view(parent::WebsiteViewHelper($this->view_path.'.index'), compact('data'));
    }

    public function galleryDetail($slug)
    {
        $data = [];
        if (!$data['row'] = WebGallery::where('slug', $slug)->Active()->first())
            return redirect()->route('web.home');

        $data['images'] = $data['row']->images()->Active()->get();

        WebGallery::where('id','=',$data['row']->id)->increment('view_count', 1);

         return view(parent::WebsiteViewHelper($this->view_path.'.gallery_detail'), compact('data'));
    }
}