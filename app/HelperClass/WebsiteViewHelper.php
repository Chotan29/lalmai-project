<?php

namespace App\HelperClass;

use App\Models\ContactDirectoryGroup;
use App\Models\Web\WebRegistrationProgramme;
use App\Traits\CommonScope;
use DB;

class WebsiteViewHelper
{
    /*traits*/
    use CommonScope;

    //Registration
    public function getProgrammeById($id)
    {
        $programme = WebRegistrationProgramme::find($id);
        if ($programme) {
            return $programme->title;
        }else{
            return "Unknown";
        }
    }

    public function getCategoryTitle($category, $data)
    {
        if ($category->parent_id == 0)
            return $category->title;
        else {

            foreach ($data as $row) {
                if ($row->id == $category->parent_id) {
                    return $row->title . '  >>  '.$category->title;
                }
            }

        }
    }

    //Contact
    public function getContactGroupById($id)
    {
        $group = ContactDirectoryGroup::find($id);
        if ($group) {
            return $group->title;
        }else{
            return "Unknown";
        }
    }


}