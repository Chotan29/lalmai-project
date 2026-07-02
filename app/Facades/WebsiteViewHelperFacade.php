<?php
/**
 * Created by PhpStorm.
 * User: Umesh Kumar Yadav
 * Date: 11/12/2017
 * Time: 9:14 AM
 */
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class WebsiteViewHelperFacade extends Facade{
    protected static function getFacadeAccessor() { return 'websiteviewhelper'; }
}