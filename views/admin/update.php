<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\bootstrap\Nav;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \dektrium\user\models\User $user
 * @var string $content
 */

$this->title = Yii::t('user', 'Update user account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('@app/views/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<header class="section-header">
    <h3><?= Html::encode($this->title) . ' "' . $user->username . '"' ?> </h3>
</header>
<div class="row">
    <div class="col-md-3">
        <div class="box-typical">
            <div class="profile-links-list">
                <?= Nav::widget([
                    'options' => [
                        'class' => 'side-menu-list side-menu-list-addon',
                        'style' => '    display: block;margin:0;'
                    ],

                    'items' => [

                        [
                            'label' => Yii::t('user', 'Account details'),
                            'url' => ['/user/admin/update', 'id' => $user->id]
                        ],

                        ['label' => 'Доп. информация', 'url' => ['/user/admin/info', 'id' => $user->id]],

                        [
                            'label' => Yii::t('user', 'Confirm'),
                            'url' => ['/user/admin/confirm', 'id' => $user->id],
                            'visible' => !$user->isConfirmed,
                            'linkOptions' => [
                                'class' => 'text-success',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to confirm this user?'),
                            ],
                        ],
                        [
                            'label' => 'Войти как этот пользователь',
                            'url' => ['/user/admin/switch', 'id' => $user->id],
                            'visible' => !$user->isExpert,
                            'linkOptions' => [
                                'data-method' => 'post',
                                'data-confirm' => 'Вы уверены что хотите переключится на этого пользователя на время этой сессии?',
                            ],
                        ],
                        [
                            'label' => 'Выслать новый пароль',
                            'url' => ['/user/admin/resend-password', 'id' => $user->id],
                            'linkOptions' => [

                                'data-method' => 'post',
                                'data-confirm' => 'Сгенерировать и выслать новый пароль пользователю?',
                            ],
                        ],
                        [
                            'label' => Yii::t('user', 'Block'),
                            'url' => ['/user/admin/block', 'id' => $user->id],
                            'visible' => !$user->isBlocked,
                            'linkOptions' => [
                                'class' => 'text-danger',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t('user', 'Unblock'),
                            'url' => ['/user/admin/block', 'id' => $user->id],
                            'visible' => $user->isBlocked,
                            'linkOptions' => [
                                'class' => 'text-success',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t('user', 'Delete'),
                            'url' => ['/user/admin/delete', 'id' => $user->id],
                            'linkOptions' => [
                                'class' => 'text-danger',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to delete this user?'),
                            ],
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-body">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>
