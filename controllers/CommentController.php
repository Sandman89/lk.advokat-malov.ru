<?php

namespace app\controllers;

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
                'only' => ['quick-edit', 'delete', 'create-workflow', 'upload'],
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
                    //'create-workflow' => ['post'],
                    'delete' => ['post', 'delete'],
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
            $file = Filemanager::find()->where(['name' => $name])->one();
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
        $commentModel = CommentModel::findOne($id);
        if ($commentModel->title)
            $commentModel->scenario = 'workflow';
        //если есть файлы у комментария, то делаем перевод файлов в правильную папку
        $files = Filemanager::find()->where(['token_comment' => $commentModel->token_comment])->all();
        if ($commentModel->load(\Yii::$app->request->post())) {
            if ($commentModel->saveComment()) {
                //если есть файлы у комментария, которые еще не сохранены
                $files = Filemanager::find()->where(['token_comment' => $commentModel->token_comment, 'id_comment' => null])->all();
                if (count($files) > 0) {
                    $root = Yii::getAlias('@webroot');
                    $newFilePath = '/attachments/' . $commentModel->entityId . '_' . self::RandomString($commentModel->entityId);
                    foreach ($files as $file) {
                        //создаем новую папку для соответствующего дела и переносим туда файл из комментария
                        if (!is_dir($root . $newFilePath)) {
                            mkdir($root . $newFilePath, 0755, true);
                        }
                        rename($root . $file->path, $root . $newFilePath . '/' . $file->name);
                        //обновляем путь до файла в таблице файлов
                        $file->path = $newFilePath . '/' . $file->name;
                        //устанавливаем id коммент для ссылки
                        $file->id_comment = $commentModel->id;
                        $file->update(false);
                    }
                }
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return $commentModel->id;
                } else {
                    return $this->redirect(['create-workflow']);
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
                'token_comment' => $commentModel->token_comment
            ]);
        } else {
            return $this->render('@app/components/comments/views/workflow', [
                'commentModel' => $commentModel,
                'files' => $files,
                'id_issue' => $commentModel->entityId,
                'token_comment' => $commentModel->token_comment
            ]);
        }
    }

    /**
     * Create a workflow
     *
     * @return array
     */
    public function actionCreateWorkflow($entity)
    {
        /* @var $commentModel CommentModel */
        $id_issue = '';
        $token_comment = '';
        if (!empty($entity))
            $id_issue = $this->getCommentAttributesFromEntity($entity)['entityId'];
        //token comment - params for synchronics file and NEW commend
        $token_comment = Yii::$app->security->generateRandomString(20);
        $commentModel = new CommentModel();
        $commentModel->scenario = 'workflow';
        $commentModel->token_comment = $token_comment;
        if ($commentModel->load(\Yii::$app->request->post())) {
            $commentModel->setAttributes($this->getCommentAttributesFromEntity($entity));
            if ($commentModel->saveComment()) {
                //если есть файлы у комментария, то делаем перевод файлов в правильную папку
                $files = Filemanager::find()->where(['token_comment' => $commentModel->token_comment])->all();
                if (count($files) > 0) {
                    $root = Yii::getAlias('@webroot');
                    $newFilePath = '/attachments/' . $id_issue . '_' . self::RandomString($id_issue);
                    foreach ($files as $file) {
                        //создаем новую папку для соответствующего дела и переносим туда файл из комментария
                        if (!is_dir($root . $newFilePath)) {
                            mkdir($root . $newFilePath, 0755, true);
                        }
                        rename($root . $file->path, $root . $newFilePath . '/' . $file->name);
                        //обновляем путь до файла в таблице файлов
                        $file->path = $newFilePath . '/' . $file->name;
                        //устанавливаем id коммент для ссылки
                        $file->id_comment = $commentModel->id;
                        $file->update(false);
                    }
                }

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return $commentModel->id;
                } else {
                    return $this->redirect(['create-workflow']);
                }
            } else {
                var_dump($commentModel->errors);
                die();
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('@app/components/comments/views/workflow', [
                'commentModel' => $commentModel,
                'id_issue' => $id_issue,
                'token_comment' => $token_comment
            ]);
        } else {
            return $this->render('@app/components/comments/views/workflow', [
                'commentModel' => $commentModel,
                'id_issue' => $id_issue,
                'token_comment' => $token_comment
            ]);
        }
    }

    /**
     * @return string
     */
    public static function RandomString($number)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 10; $i++) {
            if (($i + 1) * $number > 52)
                $num_char = 52 - ((($i + 1) * $number) % 52);
            else
                $num_char = 52 - ($i + 1) * $number;
            $randstring .= $characters[$num_char];
        }
        return $randstring;
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
                //если есть файлы у комментария, то делаем перевод файлов в правильную папку
                $files = Filemanager::find()->where(['token_comment' => $commentModel->token_comment])->all();
                if (count($files) > 0) {
                    $root = Yii::getAlias('@webroot');
                    $newFilePath = '/attachments/' . $commentModel->entityId . '_' . self::RandomString($commentModel->entityId);
                    foreach ($files as $file) {
                        //создаем новую папку для соответствующего дела и переносим туда файл из комментария
                        if (!is_dir($root . $newFilePath)) {
                            mkdir($root . $newFilePath, 0755, true);
                        }
                        rename($root . $file->path, $root . $newFilePath . '/' . $file->name);
                        //обновляем путь до файла в таблице файлов
                        $file->path = $newFilePath . '/' . $file->name;
                        //устанавливаем id коммент для ссылки
                        $file->id_comment = $commentModel->id;
                        $file->update(false);
                    }
                }
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
        if ($commentModel->markRejected()) {
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
