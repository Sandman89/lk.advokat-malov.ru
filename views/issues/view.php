<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Issues */

$this->title = 'Обзор дела: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Issues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issues-view">
    <div class="issues-view-top-block p-t-lg p-b m-b-lg">
        <div class="issues-view-container">
            <?php
            if (!Yii::$app->user->isGuest)
                if (Yii::$app->user->identity->getIsExpert()): ?>
                    <div class="row">
                        <div class="col-sm-8">
                            <h5>
                                <div class="grey-text"><span class="m-r">Работы по делу # <?php echo $model->id; ?></span>
                                    <?php echo ($model->contract_number) ? '<span class="" style="color:grey;display:inline-block;"> (номер договора: ' . $model->contract_number . ') </span>' : '' ?>
                                </div>
                            </h5>
                        </div>
                        <div class="col-sm-4">
                            <?= Html::a('Редактировать дело', ['update', 'id' => $model->id], ['class' => 'btn btn-info-outline float-right']) ?>
                        </div>
                    </div>
                <?php endif ?>
            <div class="row">
                <div class="col-sm-12">
                    <h6 class="grey-text"><?= ($model->id_category) ? \app\models\Tree::findOne($model->id_category)->getBreadcrumbs(0, '<span class="glyphicon glyphicon-menu-right" style="font-size: 11px; padding: 0 12px;"></span>') : '' ?></h6>
                    <h2><?= Html::encode($model->title) ?></h2>
                </div>
                <div class="col-sm-12">
                    <div class="issues-view-description">
                        <?= $model->description ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="issues-view-client">
                        <?php
                        if (!Yii::$app->user->isGuest)
                            if (($model->id_client) && (Yii::$app->user->identity->getIsExpert())): ?>

                                <div class="p-y-8">
                                    <div class="inline-block m-r-lg">
                                        <span class="grey-text  m-r">Клиент:</span>
                                        <span><?= $model->client[0]->username ?></span>
                                    </div>
                                    <div class="inline-block m-r">
                                        <span class="grey-text m-r">Контакты клиента:</span>
                                        <span class="m-r-lg">
                                    <i class="grey-text glyphicon glyphicon-earphone m-r-min font-14"></i>
                                    <span><?= $model->client[0]->phone ?></span>
                                </span>
                                        <span>
                                    <i class="grey-text font-icon font-icon font-icon-mail m-r-min font-14"></i>
                                    <span><?= $model->client[0]->email ?></span>
                                </span>
                                    </div>
                                </div>
                            <?php endif ?>
                    </div>
                    <div class="issues-view-judge">
                        <?php if ($model->court): ?>
                            <div class="p-y-8">
                                <div class="inline-block m-r">
                                    <span class="grey-text m-r-min">Судебный орган:</span>
                                    <span class="m-r-lg">
                                      <span><?= $model->court ?></span>
                                </span>
                                </div>
                                <div class="inline-block m-r">
                                    <span class="grey-text m-r-min">Судья:</span>
                                    <span class="m-r-lg">
                                      <span><?= $model->judge ?></span>
                                </span>
                                </div>
                                <div class="inline-block m-r">
                                    <span class="grey-text m-r-min">Дата суда:</span>
                                    <span class="m-r-lg">
                                      <span><?= date('d.m.Y h:i', strtotime($model->court_date)) ?></span>
                                </span>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="issues-view-content">
        <div class="issues-view-container">
            <div class="row">
                <div class="col-sm-12">
                    <?= \app\components\AssignWidget::widget(['users' => $model->assigns, 'title' => 'Ответственные по делу']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <h5 class="m-b-md">Хронология выполнения дела</h5>
                    <?php echo  \app\components\comments\Comment::widget([
                        'model' => $model
                    ]); ?>
                    <?php /*echo \yii2mod\comments\widgets\Comment::widget([
                        'model' => $model,
                        'commentView' => '@app/views/comments/index'
                    ]);*/ ?>
                </div>
            </div>

        </div>
    </div>
</div>
</div>