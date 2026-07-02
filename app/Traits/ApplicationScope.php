<?php
namespace App\Traits;

use App\Models\ApplicationType;

trait ApplicationScope{

    public function activeApplicationType()
    {
        $applicationType = ApplicationType::Active()->orderBy('title')->pluck('title','id')->toArray();
        return array_prepend($applicationType,'Select Application Type','');
    }

    public function getApplicationTypeById($id)
    {
        $type = ApplicationType::find($id);
        if ($type) {
            return $type->title;
        }else{
            return "";
        }
    }


}