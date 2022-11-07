<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\controllers;

use dektrium\user\Finder;
use dektrium\user\models\Profile;
use dektrium\user\models\SettingsForm;
use dektrium\user\models\User;
use dektrium\user\Module;
use dektrium\user\traits\AjaxValidationTrait;
use dektrium\user\traits\EventTrait;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * SettingsController manages updating user settings (e.g. profile, email and password).
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class SettingsController extends Controller
{
    use AjaxValidationTrait;
    use EventTrait;

    /**
     * Event is triggered before updating user's profile.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_BEFORE_PROFILE_UPDATE = 'beforeProfileUpdate';

    /**
     * Event is triggered after updating user's profile.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_AFTER_PROFILE_UPDATE = 'afterProfileUpdate';

    /**
     * Event is triggered before updating user's account settings.
     * Triggered with \dektrium\user\events\FormEvent.
     */
    const EVENT_BEFORE_ACCOUNT_UPDATE = 'beforeAccountUpdate';

    /**
     * Event is triggered after updating user's account settings.
     * Triggered with \dektrium\user\events\FormEvent.
     */
    const EVENT_AFTER_ACCOUNT_UPDATE = 'afterAccountUpdate';

    /**
     * Event is triggered before changing users' email address.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_BEFORE_CONFIRM = 'beforeConfirm';

    /**
     * Event is triggered after changing users' email address.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_AFTER_CONFIRM = 'afterConfirm';

    /**
     * Event is triggered before disconnecting social account from user.
     * Triggered with \dektrium\user\events\ConnectEvent.
     */
    const EVENT_BEFORE_DISCONNECT = 'beforeDisconnect';

    /**
     * Event is triggered after disconnecting social account from user.
     * Triggered with \dektrium\user\events\ConnectEvent.
     */
    const EVENT_AFTER_DISCONNECT = 'afterDisconnect';

    /**
     * Event is triggered before deleting user's account.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_BEFORE_DELETE = 'beforeDelete';

    /**
     * Event is triggered after deleting user's account.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_AFTER_DELETE = 'afterDelete';

    /**
     * Event is triggered before updating existing user.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_BEFORE_UPDATE = 'beforeUpdate';

    /**
     * Event is triggered after updating existing user.
     * Triggered with \dektrium\user\events\UserEvent.
     */
    const EVENT_AFTER_UPDATE = 'afterUpdate';

    /** @inheritdoc */
    public $defaultAction = 'profile';

    /** @var Finder */
    protected $finder;

    /**
     * @param string           $id
     * @param \yii\base\Module $module
     * @param Finder           $finder
     * @param array            $config
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'disconnect' => ['post'],
                    'delete'     => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['profile', 'account', 'networks', 'disconnect', 'delete','deleteImage','cropImage'],
                        'roles'   => ['@'],
                    ],
                    [
                        'allow'   => true,
                        'actions' => ['confirm'],
                        'roles'   => ['?', '@'],
                    ],
                ],
            ],

        ];
    }

    public function actions()
    {
        return [
            'deleteImage' => [
                'class' => 'demi\image\DeleteImageAction',
                'modelClass' => User::className(),
                'canDelete' => function ($model) {
                    /* @var $model \yii\db\ActiveRecord */
                    return $model->id == Yii::$app->user->id;
                },
                'redirectUrl' => function ($model) {
                    /* @var $model \yii\db\ActiveRecord */
                    // triggered on !Yii::$app->request->isAjax, else will be returned JSON: {status: "success"}
                    return ['user/account'];
                },
                'afterDelete' => function ($model) {
                    /* @var $model \yii\db\ActiveRecord */
                    // You can customize response by this function, e.g. change response:
                    if (Yii::$app->request->isAjax) {
                        Yii::$app->response->getHeaders()->set('Vary', 'Accept');
                        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;

                        return ['status' => 'success', 'message' => 'Image deleted'];
                    } else {
                        return Yii::$app->response->redirect(['user/account']);
                    }
                },
            ],
            'cropImage' => [
                'class' => 'demi\image\CropImageAction',
                'modelClass' => User::className(),
                'redirectUrl' => function ($model) {
                    /* @var $model Post */
                    // triggered on !Yii::$app->request->isAjax, else will be returned JSON: {status: "success"}
                    return ['update', 'id' => $model->id];
                },
            ],
        ];
    }


    /**
     * Displays page where user can update account settings (username, email or password).
     *
     * @return string|\yii\web\Response
     */
    public function actionAccount()
    {   $model = $this->finder->findUserById(\Yii::$app->user->identity->getId());
        $model->scenario = 'settings';
       // $model = \Yii::createObject(User::className());
        $event = $this->getUserEvent($model);

        $this->performAjaxValidation($model);

        $this->trigger(self::EVENT_BEFORE_UPDATE, $event);
        if ($model->load(\Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash('success', \Yii::t('user', 'Your account details have been updated'));
            $this->trigger(self::EVENT_AFTER_UPDATE, $event);
            return $this->refresh();
        }

        return $this->render('@app/views/admin/_account', [
            'user' => $model,
            'role'=>$model->role
        ]);
    }

    /**
     * Attempts changing user's email address.
     *
     * @param int    $id
     * @param string $code
     *
     * @return string
     * @throws \yii\web\HttpException
     */
    public function actionConfirm($id, $code)
    {
        $user = $this->finder->findUserById($id);

        if ($user === null || $this->module->emailChangeStrategy == Module::STRATEGY_INSECURE) {
            throw new NotFoundHttpException();
        }

        $event = $this->getUserEvent($user);

        $this->trigger(self::EVENT_BEFORE_CONFIRM, $event);
        $user->attemptEmailChange($code);
        $this->trigger(self::EVENT_AFTER_CONFIRM, $event);

        return $this->redirect(['account']);
    }

    /**
     * Displays list of connected network accounts.
     *
     * @return string
     */
    public function actionNetworks()
    {
        return $this->render('networks', [
            'user' => \Yii::$app->user->identity,
        ]);
    }

    /**
     * Disconnects a network account from user.
     *
     * @param int $id
     *
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\web\ForbiddenHttpException
     */
    public function actionDisconnect($id)
    {
        $account = $this->finder->findAccount()->byId($id)->one();

        if ($account === null) {
            throw new NotFoundHttpException();
        }
        if ($account->user_id != \Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }

        $event = $this->getConnectEvent($account, $account->user);

        $this->trigger(self::EVENT_BEFORE_DISCONNECT, $event);
        $account->delete();
        $this->trigger(self::EVENT_AFTER_DISCONNECT, $event);

        return $this->redirect(['networks']);
    }

    /**
     * Completely deletes user's account.
     *
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionDelete()
    {
        if (!$this->module->enableAccountDelete) {
            throw new NotFoundHttpException(\Yii::t('user', 'Not found'));
        }

        /** @var User $user */
        $user  = \Yii::$app->user->identity;
        $event = $this->getUserEvent($user);

        \Yii::$app->user->logout();

        $this->trigger(self::EVENT_BEFORE_DELETE, $event);
        $user->delete();
        $this->trigger(self::EVENT_AFTER_DELETE, $event);

        \Yii::$app->session->setFlash('info', \Yii::t('user', 'Your account has been completely deleted'));

        return $this->goHome();
    }
}
