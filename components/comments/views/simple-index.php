<?php

use app\models\Task;
use yii\helpers\ArrayHelper;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $commentModel app\components\comments\models\CommentModel */
/* @var $statusWorkObject string */
/* @var $maxLevel null|integer comments max level */
/* @var $encryptedEntity string */
/* @var $pjaxContainerId string */
/* @var $formId string comment form id */
/* @var $commentDataProvider \yii\data\ArrayDataProvider */
/* @var $listViewConfig array */
/* @var $commentWrapperId string */
?>
<?php echo lo\widgets\modal\ModalAjax::widget([
    'id' => 'comment-ajax',
    'header' => 'Результат работы',
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
                    $('#comment-ajax .modal-header>span').html('<h4 class=\"modal-title\">Редактировать запись</h4>');
                 else   
                $('#comment-ajax .modal-header>span').html('<h4 class=\"modal-title\">Результат работы</h4>');
            }
       "),],
    'url' => yii\helpers\Url::to(['/comment/comment-ajax', 'entity' => $encryptedEntity]),
    'ajaxSubmit' => true,
]);
?>
<div class="comment-wrapper" id="<?php echo $commentWrapperId; ?>">
    <?php Pjax::begin(['enablePushState' => true, 'timeout' => 2000, 'id' => $pjaxContainerId]); ?>
    <div class="comments row simple-comments">
        <div class="col-md-12 col-sm-12">
            <?php echo ListView::widget(ArrayHelper::merge(
                [
                    'dataProvider' => $commentDataProvider,
                    'layout' => "{items}\n{pager}",
                    'itemView' => '_simple-list',
                    'viewParams' => [
                        'maxLevel' => $maxLevel,
                        'statusWorkObject' => $statusWorkObject
                    ],
                    'options' => [
                        'tag' => 'ol',
                        'class' => 'comments-list comments-simple-list',
                    ],
                    'itemOptions' => [
                        'tag' => false,
                    ],
                ],
                $listViewConfig
            )); ?>
            <?php if ((!Yii::$app->user->isGuest) && ($statusWorkObject != 'completed')) : ?>
                <?php echo $this->render('_form', [
                    'class_form' => 'comment-box comment-box_simple',
                    'commentModel' => $commentModel,
                    'formId' => $formId,
                    'encryptedEntity' => $encryptedEntity,
                    'token_comment' => $token_comment,
                    'id_issue' => $id_issue,
                ]); ?>
            <?php endif; ?>
        </div>
    </div>
    <?php
    $script = new \yii\web\JsExpression("
    var jScrollOptions_to_bottom = {
                    maintainPosition:true,
                    stickToBottom:true,
                    autoReinitialise: true,
                    autoReinitialiseDelay: 100,
                    contentWidth: '0px'
                };
    $('.comments-simple-list').jScrollPane(jScrollOptions_to_bottom).data('jsp').scrollToBottom();
                
");
    $this->registerJs($script, \yii\web\View::POS_END);
    ?>
    <?php Pjax::end(); ?>
</div>
