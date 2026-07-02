<?php
/*
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rupani-1 (Province 2, Saptari), Nepal
 * +977-9868156047
 * freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */

namespace App\Http\Controllers\Web;

use View;
use AppHelper;

class WebBaseController extends WebController
{
    protected $message_success = 'message_success';
    protected $message_warning = 'message_warning';
    public $internet_status = 'There is no Internet connection. Please Check the network cables, modem, and router.';

    /*check internet connection*/
    public function checkConnection()
    {
        $connected = @fsockopen("www.google.com", 80); //website, port  (try 80 or 443)
        if ($connected){
            return true;
        }
        return false;
    }



}