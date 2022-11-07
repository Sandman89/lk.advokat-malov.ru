<?php

use lo\widgets\modal\ModalAjax;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model app\models\Task */
?>
<?php echo lo\widgets\modal\ModalAjax::widget([
    'id' => 'comment-ajax',
    'header' => 'Результат работы',
    'closeButton' => [
        'class' => 'close modal-close'
    ],
    'selector' => 'a.lo-modal',
    'autoClose' => true,

    //событие
    'events' => [
        lo\widgets\modal\ModalAjax::EVENT_MODAL_SUBMIT => new \yii\web\JsExpression("
                                                function(event, data, status, xhr, selector) {
                                                    if(status){
                                                            
                                                             $(this).modal('toggle');
                                                        }
                                                    }
                                            "),
    ],
    'url' => yii\helpers\Url::to(['/comment/comment-ajax', 'entity' => 'qwe']),
    'ajaxSubmit' => true,
]);
?>
<div class="fc-popover click" style="left: 622px; top: 504px;">
    <div class="fc-header">
        <?= $model->title ?>
        <button type="button" class="cl close modal-close" style="color: white">×</button>

    </div>
    <div class="fc-body main-screen"><span class="tbl-cell-time">
            <?= ($model->deadline_local) ? '<span class="font-icon font-icon-fire"></span> ' . $model->deadline_local : '' ?>
            <?= ($model->start_local) ? $model->start_local . ' - ' . $model->end_local : '' ?>
        </span>

        <?php if (!empty($model->assigns)) : ?>
            <span class="fc-body__title">Исполнитель</span>
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

        <?php endif; ?>
        <?= ($model->description) ? ' <span class="fc-body__title">Описание</span><div class="color-blue-grey fc-body__desc">' . $model->description . '</div>' : '' ?>
        <ul class="actions">
            <?php //todo сделать везде проверки на доступность методов в зависимости от пользователей. Разделение прав?>
            <li><?= Html::a('Детали', ['task/view', 'id' => $model->id]) ?></li>
            <?php if ($model->accessEdit): ?>
                <li><?= Html::a('Редактировать', ['/task/update', 'id' => $model->id, 'lomodal' => true], ['class' => 'lo-modal-task', 'title' => '<h4 class="modal-title">Редактировать задачу</h4>']) ?></li>
            <?php endif; ?>
            <?php if ($model->accessComplete): ?>
                <li><?= Html::a('Завершить', ['/comment/comment-ajax', 'entity' => \app\components\comments\Comment::getEncryptedEntity($model->id, get_class($model)), 'type' => 'complete', 'lomodal' => true], ['class' => 'lo-modal', 'title' => '<h4 class="modal-title">Результат работы</h4>']) ?></li>
            <?php endif; ?>
            <?php if ($model->accessRestore): ?>
                <li><?= Html::a('Восстановить', ['/task/restore', 'id' => $model->id]) ?></li>
            <?php endif; ?>
        </ul>
    </div>

</div>
