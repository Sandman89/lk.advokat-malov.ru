<?php

namespace app\controllers;

use app\components\fullcalendar\models\Event;
use app\models\Task;
use Yii;
use yii\db\ActiveQuery;
use yii\web\NotFoundHttpException;

class CalendarController extends \yii\web\Controller
{
    public function actionIndex()
    {
        if (Yii::$app->user->identity->isAdmin)
            $experts_data = \dektrium\user\models\User::find()->where(['role' => 'expert'])->all();
        else
            $experts_data = \dektrium\user\models\User::find()->where(['role' => 'expert'])->andWhere(['id' => Yii::$app->user->id])->all();
        return $this->render('index', ['experts_data' => $experts_data]);
    }

    /**
     *
     * @param type $choice
     * @return type
     */
    public function actionFilterEvents($choice = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $query = Task::find()->joinWith('assigns')->where('status != "completed"');

        if (is_null($choice)) {
            //the function should return the same events that you were loading before
            $tasks = [];

        } else {
            if ($choice == '')
                $arr_choice = '';
            else
                $arr_choice = explode(';', $choice);
            $tasks = $query->andWhere(['in', 'user.id', $arr_choice])->all();
        }
        return $this->loadEvents($tasks);
    }

    /**
     *
     * @param $tasks Task[]
     * @return \yii2fullcalendar\models\Event
     */
    private function loadEvents($tasks)
    {
        $events = null;
        foreach ($tasks AS $task) {
            if ((!empty($task->start) && (!empty($task->end))) || (!empty($task->deadline))) {


                $Event = new Event();
                $Event->id = $task->id;
                $Event->title = $task->title;
                $Event->url = '';
                //todo проблема с массивом исполнителей. Если исполнителя 2 и у каждого свой цвет. Какой выставлять цвета у задачи?
                // Нужно сделать чтобы цвета выбирались еще и из того какие адвокаты выбраны в фильтре
                $Event->backgroundColor = $task->assigns[0]->color;
                if ($task->typetask == Task::TYPETASK_BETWEEN_DATE) {
                    $Event->start = $task->start;
                    $Event->end = $task->end;
                }
                if ($task->typetask == Task::TYPETASK_DUE) {
                    $Event->start = $task->deadline;
                    $Event->durationEditable = false;
                    $Event->nonstandard = [
                        'icon' => '<span class="font-icon font-icon-fire"></span>',
                    ];
                }
                $events[] = $Event;
            }
        }
        return $events;
    }

    /**
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model = Task::findOne($id);
            if ($model->typetask == Task::TYPETASK_DUE) {
                $model->deadline = date(('Y-m-d H:i:s'), strtotime(Yii::$app->request->post('start')));
            } else {
                $model->start = date(('Y-m-d H:i:s'), strtotime(Yii::$app->request->post('start')));
                $model->end = date(('Y-m-d H:i:s'), strtotime(Yii::$app->request->post('end')));
            }
            if ($model->save()) {
                return true;
            }
        }
        return false;
    }

    /**
     * get html in ajax request for 'eventclick' fullcalendar
     * @param $id
     */
    public function actionPopover($id)
    {
        if (Yii::$app->request->isAjax) {
            $model = Task::findOne($id);

            return $this->renderAjax('popover', [
                'model' => $model,
            ]);
        }
    }

}
