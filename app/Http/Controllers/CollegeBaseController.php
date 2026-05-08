<?php
/*
 * DEVELOPER INFO:
 * ----------------------------------------------------------------------------------------------------
 * Mr. Umesh Kumar Yadav
 * Business With Technology Pvt. Ltd.
 * Rajbiraj-8 (Province 2, Saptari), Nepal
 * +977-9868156047, freelancerumeshnepal@gmail.com
 * https://codecanyon.net/item/unlimited-edu-firm-school-college-information-management-system/21850988
 */
namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use App\Models\GuardianDetail;
use App\Models\PaymentSetting;
use App\Models\Route;
use App\Models\SmsSetting;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Web\WebMenu;
use App\Models\Web\WebPage;
use App\Traits\AcademicScope;
use App\Traits\AccountingScope;
use App\Traits\ApplicationScope;
use App\Traits\CertificateScope;
use App\Traits\CommonScope;
use App\Traits\CustomerScopes;
use App\Traits\DateTimeScope;
use App\Traits\ExaminationScope;
use App\Traits\FacultySemesterScope;
use App\Traits\HostelScope;
use App\Traits\OnlineExamScopes;
use App\Traits\PurchaseVerification;
use App\Traits\StaffScope;
use App\Traits\StudentScopes;
use App\Traits\TransportScope;
use App\Traits\UploadScope;
use App\Traits\VendorScopes;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use View;
use AppHelper, Image;
use Illuminate\Support\Facades\Cache;

class CollegeBaseController extends Controller
{
    public $defaultDataFetch = 50;
    public $pagination_limit = 100;

    public $superAdminRoleId = 1;
    public $adminRoleId = 2;
    public $accountRoleId = 3;
    public $libraryRoleId = 4;
    public $staffRoleId = 5;
    public $studentRoleId = 6;
    public $guardianRoleId = 7;

    protected $CustomerRegCode = 'CUS';
    protected $VendorRegCode = 'VEN';
    protected $ProductCodeStart = 'PRO';
    protected $StaffCodeStart = 'STA';
    protected $vendorAccCategory = 85;
    protected $customerAccCategory = 86;
    protected $staffAccCategory = 76;
    protected $message_success = 'message_success';
    protected $message_warning = 'message_warning';
    protected $message_info = 'message_info';
    protected $message_danger = 'message_danger';
    public $internet_status = 'There is no Internet connection. Please Check the network cables, modem, and router.';

    /*Traits*/
    use CommonScope;
    use AcademicScope;
    use StudentScopes;
    use StaffScope;
    use DateTimeScope;
    use AccountingScope;
    use UploadScope;
    use ExaminationScope;
    use OnlineExamScopes;
    use FacultySemesterScope;
    use ApplicationScope;
    use TransportScope;
    use HostelScope;
    use VendorScopes;
    use CustomerScopes;
    use PurchaseVerification;
    use CertificateScope;

    public function __construct()
    {
//        if(auth()->user()->hasRole('super-admin')){
//            $this->getPurchaseDetail();
//        }

//        if(getenv('APP_DEBUG')=="true"){
//            session()->flash($this->message_info, 'Some Feature & Functions are Disable in Demo Mode.');
//        }
    }

    protected function loadDataToView($view_path)
    {
        View::composer($view_path, function ($view) {
            $view->with('base_route', $this->base_route);
            $view->with('view_path', $this->view_path);
            $view->with('panel', $this->panel);
            $view->with('generalSetting', $this->getGeneralSetting());
            $view->with('purchaseDetail', $this->getPurchaseDetail());
            $view->with('paymentGatewayStatus', $this->paymentGatewayStatus());
            $view->with('smsSetting', $this->getSmsSetting());
            $view->with('profileImageSrc', $this->profileImageSrc());
            $view->with('folder_name', property_exists($this, 'folder_name')?$this->folder_name:'');
           $view->with('welcome_menu', $this->welcomeNav());

        });

        return $view_path;
    }

    /*check internet connection*/
    public function checkConnection()
    {
        $connected = @fsockopen("www.google.com", 80); //website, port  (try 80 or 443)
        if ($connected){
            return true;
        }
        return false;
    }

    protected function invalidRequest($message = 'Invalid request!!')
    {
        request()->session()->flash($this->message_warning, $message);
        return redirect()->route($this->base_route);
    }

    protected function getGeneralSetting()
    {
        $data['general_setting'] = GeneralSetting::first();

        if(isset($data['general_setting']) && $data['general_setting'] != null){
            $licenseInfo = $this->getPurchaseDetail();
            if(isset($licenseInfo)) {
                $expireAt = (isset($licenseInfo->sold_at)?Carbon::parse($licenseInfo->sold_at)->addYear():'');
                //$body->expire = Carbon::parse($expire)->format('d-m-Y');
                //$expireAt = $licenseInfo->expire;
                $buyer = isset($licenseInfo->buyer)?$licenseInfo->buyer:'';
                $supportedUntil = isset($licenseInfo->supported_until)?Carbon::parse($licenseInfo->supported_until)->format('d-m-Y'):'';

                $data['general_setting']->buyer = $buyer;
                $data['general_setting']->license = $expireAt;
                $data['general_setting']->support = $supportedUntil;
            }

            //dd($data['general_setting']);

            return $data['general_setting'];
        }else{
            request()->session()->flash($this->message_warning, 'Please, Setup your institution detail or contact your system administrator');
            return new GeneralSetting();
        }
    }

    protected function paymentGatewayStatus()
    {
        $data['payment_setting'] = PaymentSetting::Active()->get();
        if(isset($data['payment_setting']) && $data['payment_setting']->count() > 0){
            return $paymentGateway = json_decode($data['payment_setting'],true);
            //$manageSetting = json_encode(array_pluck($data['payment_setting'],'status','identity'));
            //$manageSetting = collect(array_pluck($data['payment_setting'],'status','identity'));
            //$manageSetting = collect(array_pluck($data['payment_setting'],'status','identity'));
            //return $manageSetting;
        }
    }

    // protected function multiplePaymentGatewayStatus()
    // {
    //     $data['payment_setting'] = PaymentSetting::get();
    //     if(isset($data['payment_setting']) && $data['payment_setting']->count() > 0){
    //         return $paymentGateway = json_decode($data['payment_setting'],true);
    //         //$manageSetting = json_encode(array_pluck($data['payment_setting'],'status','identity'));
    //         //$manageSetting = collect(array_pluck($data['payment_setting'],'status','identity'));
    //         //$manageSetting = collect(array_pluck($data['payment_setting'],'status','identity'));
    //         //return $manageSetting;
    //     }
    // }

    protected function getPaymentSetting()
    {
        $data['payment_setting'] = PaymentSetting::where('status',1)->get();
        if(isset($data['payment_setting']) && $data['payment_setting']->count() > 0){
            $d = json_decode($data['payment_setting'],true);
            $manageSetting = array_pluck($d,'config','identity');
            return $manageSetting;
        }
    }

    protected function getSmsSetting()
    {
        $data['sms_setting'] = SmsSetting::where('status',1)->get();
        if(isset($data['sms_setting']) && $data['sms_setting']->count() > 0){
            $d = json_decode($data['sms_setting'],true);
            $manageSetting = array_pluck($d,'config','identity');
            return $manageSetting;
        }
    }


    protected function welcomeNav(){
        $data = [];
        $data['menus'] =   WebMenu::where('slug','=', 'welcome_menu')->first();
        //$data['pages'] = WebPage::where('status', 1)->orderBy('title','asc')->get();
        $data['pages'] =  Cache::remember('pages', 3600,function () {
                           WebPage::where('status', 1)->orderBy('title','asc')->get();
                        });
                        
        $data['active_pages'] = collect();
        if($data['menus'] && method_exists($data['menus'],'pages')){
            $data['active_pages'] = $data['menus']->pages()->orderBy('rank')->get();
        }
        return $data['active_pages'];
    }

    public function currentUnpaidInstallment($regNo)
    {
    //get installment amount
        $response = app()->call('App\Http\Controllers\API\PaymentController@getStudentInfo', [
            'studentId' => $regNo
        ]);

        if($response->original['success']){
            //return $response->original['payload']['installmentAmount'];
            return $response->original['payload'];
        }
    }

    public function currentUnpaidInstallmentDetail($regNo)
    {
    //get installment amount
        $response = app()->call('App\Http\Controllers\API\PaymentController@getStudentDetailInfo', [
            'studentId' => $regNo
        ]);

        if($response->original['success']){
            //return $response->original['payload']['installmentAmount'];
            return $response->original['payload'];
        }
    }

}
