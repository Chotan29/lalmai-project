<?php

namespace App\Http\Middleware;

use App\Http\Controllers\CollegeBaseController;
use Closure;

class ModuleAccess extends CollegeBaseController
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        $moduleStatus = false;

        if(request()->is('webportal/admin*')){
            if($this->getGeneralSetting()->web_cms !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('front_desk*')){
            if($this->getGeneralSetting()->front_desk !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('student_staff*')){
            if($this->getGeneralSetting()->student_staff !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('account*')){
            if($this->getGeneralSetting()->account !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('inventory*')){
            if($this->getGeneralSetting()->inventory !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('library*')){
            if($this->getGeneralSetting()->library !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('attendance*')){
            if($this->getGeneralSetting()->attendance !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('exam*')){
            if($this->getGeneralSetting()->exam !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('certificate*')){
            if($this->getGeneralSetting()->certificate !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('hostel*')){
            if($this->getGeneralSetting()->hostel !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('transport*')){
            if($this->getGeneralSetting()->transport !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('assignment*')){
            if($this->getGeneralSetting()->assignment !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('download*')){
            if($this->getGeneralSetting()->upload_download !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('meeting*')){
            if($this->getGeneralSetting()->meeting !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('alert*')){
            if($this->getGeneralSetting()->alert !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('academic*')){
            if($this->getGeneralSetting()->academic !== 1){
                $moduleStatus = true;
            }
        }

        if(request()->is('help*')){
            if($this->getGeneralSetting()->help !== 1){
                $moduleStatus = true;
            }
        }

        if($moduleStatus){
            abort(403,'Module Not Activated. Please, Contact Your Administrator');
        }else{
            return $next($request);
        }

        return $next($request);


    }

}
