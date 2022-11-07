<?php

use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Task */

$this->title = 'Обзор дела: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Task', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user'),]) ?>
<div class="row">
    <div class="col-sm-6">
        <header class="section-header">
            <h3>Задача
                № <?php echo $model->id; ?></h3> <?= $model->statuslabel; ?>
        </header>
    </div>
    <div class="col-sm-6">
        <div class="form-group row">
            <div class="col-sm-12 text-right">
                <?php if ($model->accessEdit) {
                    echo Html::a('Редактировать задачу', ['update', 'id' => $model->id], ['class' => 'btn btn-info-outline']);
                }
                ?>
                <?php if ($model->accessComplete) {
                    echo Html::a('Завершить', ['/comment/comment-ajax', 'entity' => \app\components\comments\Comment::getEncryptedEntity($model->id, get_class($model)), 'type' => 'complete'], ['class' => 'btn btn-info-outline lo-modal ']);
                }
                if ($model->accessRestore) {
                    echo Html::a('Восстановить', ['/task/restore', 'id' => $model->id], ['class' => 'btn btn-info-outline ']);
                }
                ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xxl-9 col-lg-12 col-xl-8 col-md-8">
        <div class="box-typical">
            <div class="proj-page-section">
                <h5><?= Html::encode($model->title) ?></h5>
                <?= $model->description ?>
            </div>
            <span class="font-16 comment-separete-text">Комментарии:</span>
            <?php echo \app\components\comments\Comment::widget([
                'orderby' => SORT_ASC,
                'commentView' => '@app/components/comments/views/simple-index',
                'model' => $model,
                'maxLevel' => 0
            ]); ?>
        </div>
    </div>
    <div class="col-xxl-3 col-lg-12 col-xl-4 col-md-4">
        <div class="box-typical">
            <?php if (!empty($model->deadline)): ?>
                <section class="proj-page-section proj-page-time-info">
                    <header class="proj-page-subtitle padding-sm">
                        <div class="proj-label">Сроки</div>
                    </header>
                    <div class="tbl">
                        <div class="tbl-row">
                            <div class="tbl-cell">Крайний срок</div>
                            <div class="tbl-cell tbl-cell-time"><?= $model->deadline_local ?></div>
                        </div>
                        <div class="tbl-row">
                            <div class="tbl-cell">Оставшееся время</div>
                            <div class="tbl-cell tbl-cell-time"><?= \app\components\helpers\Helpers::downcounter($model->deadline) ?></div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
            <?php if (!empty($model->start)): ?>
                <section class="proj-page-section proj-page-time-info">
                    <header class="proj-page-subtitle padding-sm">
                        <div class="proj-label">Сроки</div>
                    </header>
                    <div class="tbl">
                        <div class="tbl-row">
                            <div class="tbl-cell">Дата начала</div>
                            <div class="tbl-cell tbl-cell-time"><?= $model->start_local ?></div>
                        </div>
                        <div class="tbl-row">
                            <div class="tbl-cell">Дата завершения</div>
                            <div class="tbl-cell tbl-cell-time"><?= $model->end_local ?></div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
            <?php if (!empty($model->issue)) : ?>
                <section class="proj-page-section">
                    <header class="proj-page-subtitle padding-sm">
                        <div class="proj-label">Связанное дело</div>
                    </header>
                    <?= Html::a($model->issue->title, ['issues/view', 'id' => $model->id_issue]) ?>
                </section>
            <?php endif; ?>
            <?php if (!empty($model->author)) : ?>
                <section class="proj-page-section proj-page-assigned">
                    <header class="proj-page-subtitle padding-sm">
                        <div class="proj-label">Постановщик</div>
                    </header>
                    <div class="users">
                        <div class="user-card-row">
                            <div class="tbl-row">
                                <div class="tbl-cell tbl-cell-photo">
                                    <a href="#">
                                        <img src="<?= $model->author->getImageSrc('small_'); ?>" alt="">
                                    </a>
                                </div>
                                <div class="tbl-cell">
                                    <p class="user-card-row-name"><a href="#"><?= $model->author->username ?></a></p>
                                    <p class="color-blue-grey-lighter"><?= $model->author->company_posiotion ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
            <?php if (!empty($model->assigns)) : ?>
                <section class="proj-page-section proj-page-assigned">
                    <header class="proj-page-subtitle padding-sm">
                        <div class="proj-label">Исполнитель</div>
                    </header>
                    <div class="users">
                        <?php foreach ($model->assigns as $assign): ?>
                            <div class="user-card-row">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-photo">
                                        <a href="#">
                                            <img src="<?= $assign->getImageSrc('small_'); ?>" alt="">
                                        </a>
                                    </div>
                                    <div class="tbl-cell">
                                        <p class="user-card-row-name"><a href="#"><?= $assign->username ?></a></p>
                                        <p class="color-blue-grey-lighter"><?= $assign->company_posiotion ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
            <section class="proj-page-section proj-page-dates">
                <header class="proj-page-subtitle padding-sm">
                    <div class="proj-label">Даты</div>
                </header>
                <div class="tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-lbl">Создано:</div>
                        <div class="tbl-cell"><?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d.m.Y в H:i'); ?></div>
                    </div>
                    <div class="tbl-row">
                        <div class="tbl-cell tbl-cell-lbl">Обновлено:</div>
                        <div class="tbl-cell"><?= Yii::$app->formatter->asDatetime($model->updated_at, 'php:d.m.Y в H:i'); ?></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>