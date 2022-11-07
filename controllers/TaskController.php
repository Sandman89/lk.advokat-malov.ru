<?php

namespace app\controllers;

use Yii;
use app\models\Task;
use app\models\TaskSearch;
use yii\base\ExitException;
use yii\base\Model;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Task models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'is_archive' => false
        ]);
    }

    /**
     * Lists all Task models.
     * @return mixed
     */
    public function actionArchive()
    {
        $searchModel = new TaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,'archive');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'is_archive' => true
        ]);
    }

    /**
     * Displays a single Task model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id_issue = null, $start = null, $end = null, $allDay = null)
    {
        $model = new Task();
        $hidden_id_issue = false;
        if ($id_issue != null) {
            $model->id_issue = $id_issue;
            $hidden_id_issue = true;
        }
        if (($start) && ($end)) {
            if ($allDay == 'true') {//если весь день,то это задано с месячного календаря и нужно выставить час с 12-13
                $model->start = date(('Y-m-d 12:00:00'), strtotime($start));
                $model->end = date(('Y-m-d 13:00:00'), strtotime($end . "-1 days"));
            }
            else{
                $model->start = date(('Y-m-d H:i:s'), strtotime($start));
                $model->end = date(('Y-m-d H:i:s'), strtotime($end));
            }
        }
        if (!Yii::$app->user->isGuest) // устанавливаем дефолтного исполнителя текущего залогинегого юзера
            $model->assign_id = Yii::$app->user->identity->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $model->id;
            } else {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('create', [
                'model' => $model,
                'hidden_id_issue' => $hidden_id_issue
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'hidden_id_issue' => $hidden_id_issue
            ]);
        }

    }

    public function actionValidation()
    {
        $model = new Task();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    /**
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $lomodal = false)
    {
        $model = $this->findModel($id);
        $hidden_id_issue = false;
        if ($lomodal)
            $hidden_id_issue = true;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $model->id;
            }
            else{
                \Yii::$app->getSession()->setFlash('success', 'Задача # ' . $model->id . ' была обновлена');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('update', [
                'model' => $model,
                'hidden_id_issue' => $hidden_id_issue
            ]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'hidden_id_issue' => $hidden_id_issue
            ]);
        }
    }


    /**
     *  Restore process
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRestore($id)
    {
        $model = $this->findModel($id);
        $model->status = "worked";
        if ($model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'Задача № ' . $model->id . ' была возвращена в работу');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return false;
    }

    /**
     * Deletes an existing Task model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Task the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Performs AJAX validation.
     *
     * @param array|Model $model
     *
     * @throws ExitException
     */
    protected function performAjaxValidation($model)
    {
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

}
