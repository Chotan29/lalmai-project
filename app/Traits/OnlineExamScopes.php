<?php
namespace App\Traits;

use App\Models\McqInstruction;
use App\Models\McqQuestionGroup;
use App\Models\McqQuestionLevel;

trait OnlineExamScopes{


    public function getExamInstructions()
    {
        $group = McqInstruction::select('id', 'title')->Active()->pluck('title','id')->toArray();
        return array_prepend($group,'Select Instruction','');
    }

    public function getQuestionGroups()
    {
        $group = McqQuestionGroup::select('id', 'title')->Active()->pluck('title','id')->toArray();
        return array_prepend($group,'Select Group',0);
    }

    public function getQuestionLevel()
    {
        $level = McqQuestionLevel::select('id', 'title')->Active()->pluck('title','id')->toArray();
        return array_prepend($level,'Select Level',0);
    }

    public function getQuestionGroupById($id)
    {
        $group = McqQuestionGroup::find($id);
        if ($group) {
            return $group->title;
        }else{
            return "Unknown";
        }
    }

    public function getQuestionLevelById($id)
    {
        $group = McqQuestionLevel::find($id);
        if ($group) {
            return $group->title;
        }else{
            return "Unknown";
        }
    }



}