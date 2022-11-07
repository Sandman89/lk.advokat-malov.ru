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
        if (Yii::$app->user->identity->isClient) {
            $model = Issues::find()->one();
            return $this->redirect(['issues/view', 'id' => $model->id]);
        }
        $searchModel = new IssuesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'is_archive' => false
        ]);
    }

    /**
     * Lists all Issues models.
     * @return mixed
     */
    public function actionArchive()
    {
        $searchModel = new IssuesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'archive');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'is_archive' => true
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
                    \Yii::$app->getSession()->setFlash('success', 'Дело # ' . $model->id . ' было успешно создано');
                    return $this->redirect(['issues/view', 'id' => $model->id]);
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
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $model->id;
            } else {
                \Yii::$app->getSession()->setFlash('success', 'Дело # ' . $model->id . ' было обновлено');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }

    }

    /**
     *  Complete process
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionComplete($id, $lomodal = false)
    {
        $model = $this->findModel($id);
        $model->status = "completed";
        $model->completed_at = date('Y-m-d H:i:s', time());
        if ($model->save()) {
            if ($lomodal) {
                return $this->redirect(['index']);
            }
            \Yii::$app->getSession()->setFlash('success', 'Дело № ' . $model->id . ' завершено и помещено в архив');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return false;
        /*
        return $this->render('view', [
            'model' => $model,
        ]);*/
    }

    /**
     *  Restore process
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRestore($id, $lomodal = false)
    {
        $model = $this->findModel($id);
        $model->status = "worked";
        if ($model->save()) {
            if ($lomodal) {
                return $this->redirect(['issues/archive']);
            } else {
                \Yii::$app->getSession()->setFlash('success', 'Дело № ' . $model->id . ' было возвращено в работу');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return false;
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

    public function actionIssueList($q = null, $id = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $query = new Query;
            $query->select('id, title AS text')
                ->from('issues')
                ->where(['like', 'title', $q]);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Issues::find($id)->title];
        }
        return $out;
    }
}
