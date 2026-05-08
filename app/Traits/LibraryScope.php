<?php
namespace App\Traits;

use App\Models\AlertSetting;
use App\Models\BookCategory;
use App\Models\BookMaster;
use App\Models\BookStatus;
use App\Models\LibraryCirculation;
use App\Models\LibraryMember;
use App\Models\Staff;
use App\Models\Student;
use Illuminate\Http\Request;

trait LibraryScope{

    public function activeBookCategories()
    {
        $category = BookCategory::Active()->orderBy('title')->pluck('title','id')->toArray();
        return array_prepend($category,'Select Category','0');
    }

    public function activeBookStatus()
    {
        $status = BookStatus::select('id', 'title')->orderBy('title')->pluck('title','id')->toArray();
        return array_prepend($status,'Select Status','0');
    }

    public function activeBookMasters()
    {
        $book = BookMaster::select('id', 'title')->orderBy('title')->pluck('title','id')->toArray();
        return array_prepend($book,'Select Book','0');
    }

    /*Library Views*/
    public function getBookCategoryById($id)
    {
        $BookCategory = BookCategory::find($id);
        if ($BookCategory) {
            return $BookCategory->title;
        }else{
            return "Unknown";
        }
    }

    public function getBookMasterById($id)
    {
        $BookMaster = BookMaster::find($id);
        if ($BookMaster) {
            return $BookMaster->title;
        }else{
            return "Unknown";
        }
    }

    /*Book Status Views*/
    public function getBookStatusById($id)
    {
        $BookStatus = BookStatus::find($id);
        if ($BookStatus) {
            return $BookStatus->title;
        }else{
            return "Unknown";
        }
    }

    /*Book Status Views*/
    public function getBookStatusClassById($id)
    {
        $BookStatus = BookStatus::find($id);
        if ($BookStatus) {
            return $BookStatus->display_class;
        }else{
            return "Unknown";
        }
    }

    /*Library User Type Views*/
    public function getLibUserTypeId($id)
    {
        $userType = LibraryCirculation::find($id);
        if ($userType) {
            return $userType->user_type;
        }else{
            return "Unknown";
        }
    }


    public function sendLibraryRegistrationAlert($memberId,$userType)
    {
        //sending confirmation alert
        $emailIds = [];
        $contactNumbers = [];
        $alert = AlertSetting::select('sms','email','subject','template')->where('event','=','LibraryRegistration')->first();
        if(!$alert) {

        }else{
            if($userType == 1){
                $student = Student::select('students.id', 'students.first_name','students.email', 'ai.mobile_1')
                    ->join('addressinfos as ai', 'ai.students_id', '=', 'students.id')
                    ->find($memberId);
                //send alert
                //Dear {{first_name}}, Congratulation! You are successfully registered in our library.
                $subject = $alert->subject;
                $message = $alert->template;
                $message = str_replace('{{first_name}}', $student->first_name, $message );
                $emailIds[] = $student->email;
                $contactNumbers[] = $student->mobile_1;

                /*Now Send SMS On First Mobile Number*/
                if($alert->sms == 1){
                    $contactNumbers = $this->contactFilter($contactNumbers);
                    $smssuccess = $this->sendSMS($contactNumbers,$message);
                }

                /*Now Send Email With Subject*/
                if($alert->email == 1){
                    $emailIds = $this->emailFilter($emailIds);
                    /*sending email*/
                    $emailSuccess = $this->sendEmail($emailIds, $subject, $message);
                }
            }

            if($userType == 2){
                $staff = Staff::select('first_name','email', 'mobile_1')
                    ->find($memberId);
                //send alert
                //Dear {{first_name}}, Congratulation! You are successfully registered in our library.
                $subject = $alert->subject;
                $message = $alert->template;
                $message = str_replace('{{first_name}}', $staff->first_name, $message );
                $emailIds[] = $staff->email;
                $contactNumbers[] = $staff->mobile_1;

                /*Now Send SMS On First Mobile Number*/
                if($alert->sms == 1){
                    $contactNumbers = $this->contactFilter($contactNumbers);
                    $smssuccess = $this->sendSMS($contactNumbers,$message);
                }

                /*Now Send Email With Subject*/
                if($alert->email == 1){
                    $emailIds = $this->emailFilter($emailIds);
                    /*sending email*/
                    $emailSuccess = $this->sendEmail($emailIds, $subject, $message);
                }
            }
        }
    }
}