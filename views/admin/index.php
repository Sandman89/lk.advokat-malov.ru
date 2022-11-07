<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use lo\widgets\modal\ModalAjax;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;


/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var bool $role строка с ролью пользователя. client, user
 * @var \dektrium\user\models\UserSearch $searchModel
 */
if ($role == 'client')
    $this->title = 'Список клиентов';
else
    $this->title = 'Список сотрудников';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php echo lo\widgets\modal\ModalAjax::widget([
    'id' => 'user-ajax',
    'header' => 'Создать пользователя',
    'closeButton' => [
        'class' => 'close modal-close'
    ],
    'selector' => 'a.lo-modal-user',
    'autoClose' => true,
    'pjaxContainer' => '#user-pjax',
    //событие
    'events' => [
        lo\widgets\modal\ModalAjax::EVENT_MODAL_SUBMIT => new \yii\web\JsExpression("
                                                function(event, data, status, xhr, selector) {
                                                    if(status){
                                                             $.pjax.reload({container: '#user-pjax', async: false});
                                                             $(this).modal('toggle');
                                                             $(this).modal('handleUpdate');
                                                        }
                                                       
                                                    }
                                           ")],
    'ajaxSubmit' => true,
]);
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
    <div class="col-sm-10">
        <header class="section-header">
            <h3><?= Html::encode($this->title) ?> </h3>
        </header>
    </div>
    <div class="col-sm-2">
        <div class="form-group row">
            <div class="col-sm-12 text-right">
                <?php if ($role == 'client')
                    echo Html::a('Создать клиента', ['admin/create', 'role' => 'client'], ['class' => 'btn btn-success lo-modal-user', 'title' => '<h4 class="modal-title">Создать клиента</h4>']);
                else
                    echo Html::a('Создать сотрудника', ['admin/create', 'role' => 'expert'], ['class' => 'btn btn-success lo-modal-user', 'title' => '<h4 class="modal-title">Создать сотрудника</h4>'])
                ?>
            </div>
        </div>
    </div>
</div>

<?php Pjax::begin(['id' => 'user-pjax', 'timeout' => 2000, 'enablePushState' => false]); ?>
<div class="box-typical">
    <?= GridView::widget([
        'id' => 'user-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'tbl-typical tbl-typical-min'],
        'layout' => '{items}{pager}',
        'pager' => [
            'linkOptions' => [
                'class' => 'page-link'
            ],
            'disabledListItemSubTagOptions' => ['tag' => 'span', 'class' => 'page-link'],
            'prevPageLabel' => 'в начало',
            'nextPageLabel' => 'в конец',
            'pageCssClass' => 'paginate_button page-item',
            'nextPageCssClass' => 'paginate_button page-item next',    // Set CSS class for the “next” page button
            'prevPageCssClass' => 'paginate_button page-item previous',    // Set CSS class for the “previous” page button
        ],
        'columns' => [
            [
                'attribute' => 'username',
                'filterInputOptions' => [
                    'placeholder' => 'поиск...',
                    'class' => 'form-control',
                ],
                'format' => 'html',
                'label' => 'Имя пользователя (ФИО)',
                'content' => function ($data) {
                    $out = Html::a('<span class="table-title">' . $data->username . '</span>', ['admin/view', 'id' => $data->id], ['class' => 'black-link']);
                    if ($data->IsExpert) {
                        $out = '<div class="user-card-row">
                            <div class="tbl-row">
                                <div class="tbl-cell tbl-cell-photo">
                                ' . Html::a('<img src="' . $data->getImageSrc("small_") . '" alt="">', ['admin/view', 'id' => $data->id]) . '                         
                                </div>
                                <div class="tbl-cell">
                                    <p class="user-card-row-name">' . Html::a($data->username, ['admin/view', 'id' => $data->id]) . '</p>
                                    <p class="color-blue-grey-lighter">' . $data->company_posiotion . '</p>
                                    '.(($data->IsAdmin) ? '<span class="label label-primary">Администратор</span>' : '').'
                                </div>
                            </div>
                        </div>';
                    }
                    return $out;
                },
            ],
            [
                'filterInputOptions' => [
                    'placeholder' => 'поиск...',
                    'class' => 'form-control',
                ],
                'attribute' => 'email',
            ],
            [
                'filterInputOptions' => [
                    'placeholder' => 'поиск...',
                    'class' => 'form-control',
                ],
                'attribute' => 'phone',
            ],
            [
                'filterInputOptions' => [
                    'placeholder' => 'поиск...',
                    'class' => 'form-control',
                ],
                'attribute' => 'created_at',
                'value' => function ($model) {
                    if (extension_loaded('intl')) {
                        return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at]);
                    } else {
                        return date('Y-m-d G:i:s', $model->created_at);
                    }
                },
            ],
            [
                'filterInputOptions' => [
                    'placeholder' => 'поиск...',
                    'class' => 'form-control',
                ],
                'attribute' => 'last_login_at',
                'value' => function ($model) {
                    if (!$model->last_login_at || $model->last_login_at == 0) {
                        return Yii::t('user', 'Never');
                    } else if (extension_loaded('intl')) {
                        return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->last_login_at]);
                    } else {
                        return date('Y-m-d G:i:s', $model->last_login_at);
                    }
                },
            ],
            [
                'header' => 'Статус',
                'content' => function ($model) {
                    $out = '';
                    if ($model->isConfirmed) {
                        $out .= '<div class="text-center">
                                <span class="color-green">' . Yii::t('user', 'Confirmed') . '</span>
                            </div>';
                    } else {
                        $out .= '<div class="text-center">
                                <span class="color-red">' . Yii::t('user', 'Confirmed') . '</span>
                            </div>';
                    }
                    $out .= '<span style="border-bottom:1px solid"></span>';
                    if ($model->isBlocked) {
                        $out .= '<div class="text-center">
                                <span class="color-red">Заблокирован</span>
                            </div>';
                    }

                    return $out;
                },
                'format' => 'html',
                'visible' => Yii::$app->getModule('user')->enableConfirmation,
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{all}',
                'buttons' => [
                    'all' => function ($url, $model, $key) {
                        return \yii\bootstrap\ButtonDropdown::widget([
                            'encodeLabel' => false, // if you're going to use html on the button label
                            'label' => false,
                            'dropdown' => [
                                'encodeLabels' => false, // if you're going to use html on the items' labels
                                'items' => [
                                    [
                                        'label' => 'Заблокировать',
                                        'url' => ['block', 'id' => $key],
                                        'linkOptions' => [
                                            'data' => [
                                                'method' => 'post',
                                                'confirm' => Yii::t('user', 'Are you sure you want to block this user?'),
                                            ],
                                            'class' => 'color-red',
                                        ],
                                        'options' => [
                                            'class' => 'dropdown-item', // right dropdown
                                        ],
                                        'visible' => (!$model->isBlocked && !$model->isAdmin),   //
                                    ],
                                    [
                                        'label' => 'Разблокировать',
                                        'url' => ['block', 'id' => $key],
                                        'linkOptions' => [
                                            'data' => [
                                                'method' => 'post',
                                                'confirm' => Yii::t('user', 'Are you sure you want to unblock this user?'),
                                            ],
                                            'class' => 'color-green',
                                        ],
                                        'options' => [
                                            'class' => 'dropdown-item', // right dropdown
                                        ],
                                        'visible' => ($model->isBlocked && !$model->isAdmin),   //
                                    ],
                                    [
                                        'label' => Yii::t('user', 'Confirm'),
                                        'url' => ['confirm', 'id' => $key],
                                        'linkOptions' => [
                                            'data' => [
                                                'method' => 'post',
                                                'confirm' => Yii::t('user', 'Are you sure you want to confirm this user?'),
                                            ],
                                            'class' => 'color-green',
                                        ],
                                        'options' => [
                                            'class' => 'dropdown-item', // right dropdown
                                        ],
                                        'visible' => (!$model->isConfirmed),   //
                                    ],
                                    [
                                        'label' => Yii::t('user', 'Become this user'),
                                        'url' => ['/user/admin/switch', 'id' => $key],
                                        'linkOptions' => [
                                            'data' => [
                                                'method' => 'post',
                                                'confirm' => Yii::t('user', 'Are you sure you want to switch to this user for the rest of this Session?'),
                                            ],
                                        ],
                                        'options' => [
                                            'class' => 'dropdown-item', // right dropdown
                                        ],
                                        'visible' => ((\Yii::$app->user->identity->isAdmin || \Yii::$app->user->identity->isExpert)  && !$model->isAdmin && $model->id != Yii::$app->user->id && Yii::$app->getModule('user')->enableImpersonateUser),   //
                                    ],
                                    [
                                        'label' => 'Выслать новый пароль',
                                        'url' => ['resend-password', 'id' => $key],
                                        'linkOptions' => [
                                            'data' => [
                                                'method' => 'post',
                                                'confirm' => Yii::t('user', 'Are you sure?'),
                                            ],
                                        ],
                                        'options' => [
                                            'class' => 'dropdown-item', // right dropdown
                                        ],
                                       // 'visible' => (\Yii::$app->user->identity->isAdmin && !$model->isAdmin),   //
                                    ],

                                    [
                                        'label' => \Yii::t('yii', 'Update'),
                                        'url' => ['update', 'id' => $key],

                                        'options' => [
                                            'class' => 'dropdown-item', // right dropdown
                                        ],
                                         'visible' => (!$model->isAdmin),   //
                                    ],
                                    [
                                        'label' => \Yii::t('yii', 'Delete'),
                                        'linkOptions' => [
                                            'data' => [
                                                'method' => 'post',
                                                'confirm' => \Yii::t('yii', 'Are you sure you want to delete this item?'),
                                            ],
                                        ],
                                        'url' => ['delete', 'id' => $key],
                                        'options' => [
                                            'class' => 'dropdown-item', // right dropdown
                                        ],
                                        'visible' => (!$model->isAdmin),   //
                                    ],
                                ],
                                'options' => [
                                    'class' => 'dropdown-menu-right', // right dropdown
                                ],
                            ],
                            'options' => [
                                'class' => 'btn-no-border-color',   // btn-success, btn-info, et cetera
                            ],
                            'split' => false,    // if you want a split button
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
    <div class="clearfix"></div>
</div>
<?php Pjax::end() ?>
