<?php

use yii\helpers\ArrayHelper;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $commentModel app\components\comments\models\CommentModel */
/* @var $maxLevel null|integer comments max level */
/* @var $encryptedEntity string */
/* @var $pjaxContainerId string */
/* @var $formId string comment form id */
/* @var $commentDataProvider \yii\data\ArrayDataProvider */
/* @var $listViewConfig array */
/* @var $commentWrapperId string */
?>
<?php echo lo\widgets\modal\ModalAjax::widget([
    'id' => 'create-workflow',
    'header' => '<h4 class="modal-title">Добавить этап выполнения дела</h4>',

    'selector' => 'a.lo-modal',
    'closeButton' => [
        'label' => '<i class="font-icon-close-2"></i>',
        'class' => 'modal-close'
    ],
    'autoClose' => true,
    'pjaxContainer' => '#' . $pjaxContainerId,
    //событие
    'events' => [
        lo\widgets\modal\ModalAjax::EVENT_MODAL_SUBMIT => new \yii\web\JsExpression("
                                                function(event, data, status, xhr, selector) {
                                                    if(status){
                                                             $.pjax.reload({container: '#" . $pjaxContainerId . "', async: false});
                                                             $(this).modal('toggle');
                                                        }
                                                       
                                                    }
                                            "),],
    'url' => yii\helpers\Url::to(['/comment/create-workflow', 'entity' => $encryptedEntity]),
    'ajaxSubmit' => true,
]);
?>
<?php echo Html::a('Добавить',['/comment/create-workflow', 'entity' => $encryptedEntity],['class'=>'btn btn-success lo-modal' ]); ?>
<div class="comment-wrapper" id="<?php echo $commentWrapperId; ?>">
    <?php Pjax::begin(['enablePushState' => false, 'timeout' => 20000, 'id' => $pjaxContainerId]); ?>
    <div class="comments row">
        <div class="col-md-12 col-sm-12">
            <?php echo ListView::widget(ArrayHelper::merge(
                [
                    'dataProvider' => $commentDataProvider,
                    'layout' => "{items}\n{pager}",
                    'itemView' => '_list',
                    'viewParams' => [
                        'maxLevel' => $maxLevel,
                    ],
                    'options' => [
                        'tag' => 'ol',
                        'class' => 'comments-list list-activity',
                    ],
                    'itemOptions' => [
                        'tag' => false,
                    ],
                ],
                $listViewConfig
            )); ?>
            <?php if (!Yii::$app->user->isGuest) : ?>
                <?php echo $this->render('_form', [
                    'commentModel' => $commentModel,
                    'formId' => $formId,
                    'encryptedEntity' => $encryptedEntity,
                ]); ?>
            <?php endif; ?>
        </div>
    </div>
    <?php Pjax::end(); ?>
</div>
