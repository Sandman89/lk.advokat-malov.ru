<?php

namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class AssignWidget extends Widget
{
    public $users;
    public $title;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if (count($this->users) > 0) {
            return $this->render('assigns',['users'=>$this->users,'title'=>$this->title]);
        }
    }
}