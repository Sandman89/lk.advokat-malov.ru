<?php

namespace app\components;

use Yii;
use yii\base\Widget;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

class TaskWidget extends Widget
{
    public $id_issue;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if (!empty($this->id_issue)) {

            $query = \app\models\Task::find()->where(['id_issue'=>$this->id_issue]);
            $provider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => 20,
                ],
                'sort' => [
                    'defaultOrder' => [
                        'completed_at' => SORT_ASC,
                    ]
                ],
            ]);

            return $this->render('tasks-widget',['dataProvider'=>$provider,'id_issue'=>$this->id_issue]);

        }
    }
}