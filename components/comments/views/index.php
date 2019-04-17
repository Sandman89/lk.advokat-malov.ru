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
    'header' => 'Добавить этап выполнения дела',
    'closeButton' => [
        'class' => 'close modal-close'
    ],
    'selector' => 'a.lo-modal',
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
                                            "),
        lo\widgets\modal\ModalAjax::EVENT_MODAL_SHOW => new \yii\web\JsExpression("
            function(event, data, status, xhr, selector) {
                if (selector.hasClass('lo-modal-edit'))
                    $('#create-workflow .modal-header>span').html('<h4 class=\"modal-title\">Редактировать запись</h4>');
                 else   
                $('#create-workflow .modal-header>span').html('<h4 class=\"modal-title\">Добавить этап выполнения дела</h4>');
            }
       "),],
    'url' => yii\helpers\Url::to(['/comment/create-workflow', 'entity' => $encryptedEntity]),
    'ajaxSubmit' => true,
]);
?>
<?php echo Html::a('Добавить',['/comment/create-workflow', 'entity' => $encryptedEntity],['class'=>'btn btn-success lo-modal' ]); ?>
<div class="comment-wrapper" id="<?php echo $commentWrapperId; ?>">
    <?php Pjax::begin(['enablePushState' => true, 'timeout' => 2000, 'id' => $pjaxContainerId]); ?>
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
                    'token_comment' => $token_comment,
                    'id_issue' => $id_issue,
                ]); ?>
            <?php endif; ?>
        </div>
    </div>
    <?php Pjax::end(); ?>
</div>
