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

use App\Http\Controllers\Web\WebController;
use App\Models\Web\WebBlogCategory;
use App\Models\Web\WebContactUsSetting;
use App\Models\Web\WebGeneralSetting;
use App\Models\Web\WebHomeSetting;
use App\Models\Web\WebMenu;
use App\Models\Web\WebPage;
use App\Traits\CommonScope;
use View, URL;

class WebsiteBaseController extends WebController
{

    protected $message_success = 'message_success';
    protected $message_warning = 'message_warning';
    public $pagination_limit = 10;

    use CommonScope;


    protected function WebsiteViewHelper($view_path)
    {
        View::composer($view_path, function ($view) {
            $view->with('view_path', property_exists($this, 'view_path')?$this->view_path:'');
            $view->with('current_URL', $this->getCurrentURL());
            $view->with('categories', $this->getCategoryList());
            $view->with('generalSetting', $this->getGeneralSetting());
            $view->with('homeSetting', $this->getHomeSetting());
            $view->with('top_nav', $this->topNav());
            $view->with('main_nav', $this->mainMenu());
            $view->with('sticky_nav', $this->stickyMenu());
            $view->with('useful_links', $this->usefulLinks());
            $view->with('footer_nav', $this->footerNav());
            $view->with('contact_info', $this->contactInfo());

        });

        return $view_path;
    }

    protected function getCurrentURL(){
        return $data['url'] = URL::current();
    }

    protected function getCategoryList()
    {
        $categories = WebBlogCategory::select('id', 'title', 'slug', 'parent_id')->Active()->get();
        return $categories;
    }

    protected function getCategoryWithSubCategoryList()
    {
        $data = [];
        $data['categories'] = WebBlogCategory::select('id', 'title', 'slug')
            ->where('parent_id', 0)
            ->Active()
            ->orderBy('title')
            ->get();

        foreach ($data['categories'] as $key => $category) {

            $child_cat = WebBlogCategory::select('title', 'slug')
                ->where('parent_id', $category->id)
                ->Active()
                ->orderBy('title')
                ->get();

            $data['categories'][$key]->child = $child_cat;

        }

        return $data['categories'];

    }

    protected function getGeneralSetting()
    {
        $data['general_setting'] = WebGeneralSetting::first();
        return $data['general_setting'];
    }

    protected function getHomeSetting()
    {
        if($this->getGeneralSetting()){
            return $data['home_setting'] = WebHomeSetting::first();
        }
    }

    protected function topNav(){
        if(isset($this->getGeneralSetting()->top_nav_status) && $this->getGeneralSetting()->top_nav_status ==1){
            $data = [];
            $data['menus'] =   WebMenu::where('slug','=', 'top_menu')->first();
            $data['pages'] = WebPage::where('status', 1)->orderBy('title','asc')->get();
            $data['active_pages'] = collect();
            if($data['menus'] && method_exists($data['menus'], 'pages')){
                $data['active_pages'] = $data['menus']->pages()->orderBy('rank')->get();
            }
            return $data['active_pages'];
        }
    }

    protected function mainMenu(){
        if(isset($this->getGeneralSetting()->top_nav_status) && $this->getGeneralSetting()->main_nav_status ==1){
            $data = [];
            $data['menus'] = WebMenu::where('slug','=', 'main_navigation_menu')->first();
            $data['pages'] = WebPage::where('status', 1)->orderBy('title','asc')->get();
            $data['active_pages'] = collect();
            if($data['menus'] && method_exists($data['menus'], 'pages')){
                $data['active_pages'] = $data['menus']->pages()->orderBy('rank')->get();
            }
            return $data['active_pages'];
        }
    }

    protected function stickyMenu(){
        if(isset($this->getGeneralSetting()->top_nav_status) && $this->getGeneralSetting()->sticky_nav_status ==1){
            $data = [];
            $data['menus'] = WebMenu::where('slug','=', 'sticky_navigation_menu')->first();
            $data['pages'] = WebPage::where('status', 1)->orderBy('title','asc')->get();
            $data['active_pages'] = collect();
            if($data['menus'] && method_exists($data['menus'], 'pages')){
                $data['active_pages'] = $data['menus']->pages()->orderBy('rank')->get();
            }
            return $data['active_pages'];
        }

    }

     protected function usefulLinks(){
         if(isset($this->getGeneralSetting()->top_nav_status) && $this->getGeneralSetting()->quick_nav_status ==1){
             $data = [];
             $data['menus'] =   WebMenu::where('slug','=', 'useful_links')->first();
             $data['pages'] = WebPage::where('status', 1)->orderBy('title','asc')->get();
             $data['active_pages'] = collect();
             if($data['menus'] && method_exists($data['menus'], 'pages')){
                 $data['active_pages'] = $data['menus']->pages()->orderBy('rank')->get();
             }
             return $data['active_pages'];
         }

    }

     protected function footerNav(){
         if(isset($this->getGeneralSetting()->top_nav_status) && $this->getGeneralSetting()->footer_nav_status ==1){
             $data = [];
             $data['menus'] =   WebMenu::where('slug','=', 'footer_menu')->first();
             $data['pages'] = WebPage::where('status', 1)->orderBy('title','asc')->get();
             $data['active_pages'] = collect();
             if($data['menus'] && method_exists($data['menus'], 'pages')){
                 $data['active_pages'] = $data['menus']->pages()->orderBy('rank')->get();
             }
             return $data['active_pages'];
         }
    }

    protected function contactInfo(){
         $data = [];
        $data['contact_info'] = WebContactUsSetting::first();
        return $data['contact_info'];
    }


}
