<?php

/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\DeveloperInfo;
use App\Http\Controllers\CollegeBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image, URL;
use Swagger\Object\Schema;
use ViewHelper;
use Illuminate\Support\Facades\Route;

class DeveloperInfoController extends CollegeBaseController
{
    protected $base_route = 'developer-info';
    protected $view_path = 'super-suit.developer-info';
    protected $panel;
    protected $filter_query = [];

    public function __construct()
    {
        $this->panel = __('admin_control.child.developer_info');
    }

    public function index(Request $request)
    {
        $data = [];
        $data['routes-info'] = collect(\Illuminate\Support\Facades\Route::getRoutes())
                                ->reject(function ($item) {
                                    return starts_with($item->uri, 'horizon');
                                })
                                ->reject(function ($item) {
                                    return starts_with($item->uri, 'nova');
                                })
                                ->reject(function ($item) {
                                    return starts_with($item->uri, '__clockwork');
                                })
                                ->reject(function ($item) {
                                    return starts_with($item->uri, 'themsaid');
                                })
                                ->pluck('uri')
                                ->count();
        $data['tables-info'] = count(DB::select('SHOW TABLES'));
        return view(parent::loadDataToView($this->view_path.'.index'), compact('data'));

    }

    public function routesInfo()
    {
        $this->panel = __('admin_control.child.developer_info').'| Routes Info ';
        $data = [];
        $data['route-info'] = Route::getRoutes();
        return view(parent::loadDataToView($this->view_path.'.routes'), compact('data'));

    }

    public function tablesInfo()
    {
        $this->panel = __('admin_control.child.developer_info').'| Tables Info ';
//        try{
//                        $tables = DB::select('SHOW TABLES');
//                        foreach($tables as $key => $table){
//                            $table_names[] = $table->Tables_in_devtrigger; // Table_in_{{your database name in my case database name is devtrigger}}
//                            }
//                            return $table_names;
//                        }
//                        catch(\Exception $e){
//                            return false;
//                        }

        $tables_in_db = DB::select('SHOW TABLES');
        $db = "Tables_in_".env('DB_DATABASE');
        $tables = [];
        foreach($tables_in_db as $table){
            $table = $table->{$db};
            $tableFields = DB::getSchemaBuilder()->getColumnListing($table);
            $tables[] = [
                'table' => $table,
                'fields' => $tableFields
            ];
        }
        $data = [];
        $data['tables-info'] = $tables;
        //dd($data['tables-info']);
        return view(parent::loadDataToView($this->view_path.'.tables'), compact('data'));

    }



}
