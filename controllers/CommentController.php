<?php

namespace app\controllers;

use app\components\fullcalendar\models\Event;
use app\models\Filemanager;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii2mod\comments\events\CommentEvent;
use app\components\comments\models\CommentModel;
use yii2mod\editable\EditableAction;
use yii\web\UploadedFile;

/**
 * Class DefaultController
 *
 * @package yii2mod\comments\controllers
 */
class CommentController extends Controller
{

    //  public $layout = '@app/views/layouts/main';
    /**
     * Event is triggered before creating a new comment.
     * Triggered with yii2mod\comments\events\CommentEvent
     */
    const EVENT_BEFORE_CREATE = 'beforeCreate';

    /**
     * Event is triggered after creating a new comment.
     * Triggered with yii2mod\comments\events\CommentEvent
     */
    const EVENT_AFTER_CREATE = 'afterCreate';

    /**
     * Event is triggered before deleting the comment.
     * Triggered with yii2mod\comments\events\CommentEvent
     */
    const EVENT_BEFORE_DELETE = 'beforeDelete';

    /**
     * Event is triggered after deleting the comment.
     * Triggered with yii2mod\comments\events\CommentEvent
     */
    const EVENT_AFTER_DELETE = 'afterDelete';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'quick-edit' => [
                'class' => EditableAction::class,
                'modelClass' => CommentModel::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['quick-edit', 'delete', 'comment-ajax', 'upload'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    //'create' => ['post'],
                    //'comment-ajax' => ['post'],
                    //  'delete' => ['delete'],
                ],
            ],
            'contentNegotiator' => [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['create'],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Upload files in comment
     *
     */
    public function actionUpload()
    {
        if ($_FILES) {
            $files = Filemanager::SaveTempAttachments($_FILES);
            $file = new Filemanager();
            $file->load($_POST);
            $file->name = $files['shortname'];
            $file->original_name = $files['original_name'];
            $file->path = $files['path'];
            $file->ext = $files['ext'];
            $file->type = $files['type'];
            $file->save();
            return json_encode($files);
            $result = ['files' => $files];
            Yii::$app->response->format = trim(Response::FORMAT_JSON);
            return $result;
        } else {
            return null;
        }

    }

    /**
     * Uploaded Images Delete Action on Update Forms Action
     * @return boolean
     */
    public function actionDeleteFile()
    {

        $key = $_POST['key'];
        if ($key > 0) {
            $file = Filemanager::find()->where(['id' => $key])->one();
        } else {
            $name = $_POST['name'];
            $original_name = substr($name, 0, strrpos($name, '.'));
            $file = Filemanager::find()->where(['original_name' => $original_name, 'id_comment' => null])->one();
        }

        unlink(Yii::getAlias('@webroot') . $file->path);
        $file->delete();
        return true;

    }

    /**
     * @param $id
     * @return int|string|Response
     */
    public function actionUpdate($id)
    {
        $complete = false;
        $commentModel = CommentModel::findOne($id);
        if ($commentModel->title)
            $commentModel->scenario = 'workflow';
        //для вывода в превью при обновлении записии
        $files = Filemanager::find()->where(['token_comment' => $commentModel->token_comment])->all();
        if ($commentModel->load(\Yii::$app->request->post())) {
            if ($commentModel->saveComment()) {
                //если есть временные файлы у комментария, которые еще не сохранены. То переносим их в постоянную папку
                Filemanager::SaveConstAttachment($commentModel);
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return $commentModel->id;
                } else {
                    return $this->redirect(['comment-ajax']);
                }
            } else {
                var_dump($commentModel->errors);
                die();
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('@app/components/comments/views/workflow', [
                'commentModel' => $commentModel,
                'files' => $files,
                'id_issue' => $commentModel->entityId,
                'token_comment' => $commentModel->token_comment,
                'complete' => $complete,
            ]);
        }
    }

    /**
     * Create a workflow
     * получаем на вход $type комментария. По ним строится логика. Какой тип комментариев будет
     * @return array
     */
    public function actionCommentAjax($entity = null, $type = null, $lomodal = false)
    {
        $id_issue = '';
        $complete = false;
        if ($entity)
            $id_issue = $this->getCommentAttributesFromEntity($entity)['entityId'];
        //token comment - params for synchronics file and NEW commend
        $token_comment = Yii::$app->security->generateRandomString(20);
        $commentModel = new CommentModel();
        $commentModel->token_comment = $token_comment;
        if ($type != null) {
            if ($type == 'complete') {
                $complete = true;
                $commentModel->type = 'complete';
            }
            if ($type == 'workflow') {
                $commentModel->scenario = 'workflow';
            }
        }
        if ($commentModel->load(\Yii::$app->request->post())) {
            $commentModel->setAttributes($this->getCommentAttributesFromEntity($entity));
            if ($commentModel->saveComment()) {
                //если есть временные файлы у комментария, которые еще не сохранены. То переносим их в постоянную папку
                Filemanager::SaveConstAttachment($commentModel);
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    if ($commentModel->type == 'complete') {
                        if ($lomodal)//если был запрос из модального окна. Тогда редирект делать нельзя, нужно вернуть id issue
                                return $id_issue;
                        else{
                            \Yii::$app->getSession()->setFlash('success', 'Задача № ' . $id_issue . ' была помещена в архив');
                            return $this->redirect(['task/view', 'id' => $id_issue]);
                        }
                    }
                    return $commentModel->id;

                } else {
                    return $this->redirect(['comment-ajax']);
                }
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('@app/components/comments/views/workflow', [
                'commentModel' => $commentModel,
                'id_issue' => $id_issue,
                'token_comment' => $token_comment,
                'complete' => $complete,
            ]);
        }
    }


    /**
     * Create a comment.
     *
     * @param $entity string encrypt entity
     *
     * @return array
     */
    public
    function actionCreate($entity)
    {
        /* @var $commentModel CommentModel */
        $commentModel = new CommentModel();
        $event = Yii::createObject(['class' => CommentEvent::class, 'commentModel' => $commentModel]);
        $commentModel->setAttributes($this->getCommentAttributesFromEntity($entity));
        $this->trigger(self::EVENT_BEFORE_CREATE, $event);
        if ($commentModel->load(Yii::$app->request->post())) {
            if ($commentModel->saveComment()) {
                //если есть временные файлы у комментария, которые еще не сохранены. То переносим их в постоянную папку
                Filemanager::SaveConstAttachment($commentModel);
                $this->trigger(self::EVENT_AFTER_CREATE, $event);
                return ['status' => 'success'];
            } else {
                var_dump($commentModel->errors);
                die();
            }
        }
        return [
            'status' => 'error',
            'errors' => ActiveForm::validate($commentModel),
        ];
    }

    /**
     * Delete comment.
     *
     * @param int $id Comment ID
     *
     * @return string Comment text
     */
    public
    function actionDelete($id)
    {
        $commentModel = CommentModel::findOne($id);
        if (($commentModel->parentId == null) ? $commentModel->deleteWithChildren() : $commentModel->delete()) {
            return Yii::t('yii2mod.comments', 'Comment has been deleted.');
        } else {
            Yii::$app->response->setStatusCode(500);

            return Yii::t('yii2mod.comments', 'Comment has not been deleted. Please try again!');
        }
    }


    /**
     * Get list of attributes from encrypted entity
     *
     * @param $entity string encrypted entity
     *
     * @return array|mixed
     *
     * @throws BadRequestHttpException
     */
    protected
    function getCommentAttributesFromEntity($entity)
    {
        $decryptEntity = Yii::$app->getSecurity()->decryptByKey(utf8_decode($entity), 'comment');
        if ($decryptEntity !== false) {
            return Json::decode($decryptEntity);
        }

        throw new BadRequestHttpException(Yii::t('yii2mod.comments', 'Oops, something went wrong. Please try again later.'));
    }
}
