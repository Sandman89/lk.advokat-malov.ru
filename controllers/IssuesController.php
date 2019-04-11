<?php

namespace app\controllers;

use dektrium\user\models\User;
use Yii;
use app\models\Issues;
use app\models\IssuesSearch;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * IssuesController implements the CRUD actions for Issues model.
 */
class IssuesController extends Controller
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
     * Lists all Issues models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new IssuesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Issues model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            //'model' => Issues::find()->with(['client','assigns'])->where(['id'=>$id])->one()
        ]);
    }

    /**
     * Creates a new Issues model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Issues();
        if (!Yii::$app->user->isGuest)
            $model->assign_id = Yii::$app->user->identity->id;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return $model->id;
                } else {
                    if (isset($_POST['button-state'])) {
                        if ($_POST['button-state'] == 'go')
                            return $this->redirect(['view', 'id' => $model->id]);
                        if ($_POST['button-state'] == 'create'){
                            \Yii::$app->getSession()->setFlash('success', 'Дело # '.$model->id.' было успешно создано');
                            return $this->redirect(['create']);
                        }
                    }
                    //return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                var_dump($model->errors);
                die();
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Issues model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (isset($_POST['button-state'])) {
                if ($_POST['button-state'] == 'go')
                    return $this->redirect(['view', 'id' => $model->id]);
                if ($_POST['button-state'] == 'create'){
                    \Yii::$app->getSession()->setFlash('success', 'Дело # '.$model->id.' было обновлено');
                }
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
        else{
            return $this->render('update', [
                'model' => $model,
            ]);
        }

    }

    /**
     * Deletes an existing Issues model.
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
     * Finds the Issues model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Issues the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Issues::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
