<?php

use app\models\TaskSearch;
use lo\widgets\modal\ModalAjax;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $id_issue int */

?>
<?php echo lo\widgets\modal\ModalAjax::widget([
    'id' => 'task-ajax',
    'header' => 'Создать задачу',
    'closeButton' => [
        'class' => 'close modal-close'
    ],
    'selector' => 'a.lo-modal-task',
    'autoClose' => true,
    'pjaxContainer' => '#task-widget',
    //событие
    'events' => [
        lo\widgets\modal\ModalAjax::EVENT_MODAL_SUBMIT => new \yii\web\JsExpression("
                                                function(event, data, status, xhr, selector) {
                                                    if(status){
                                                             $.pjax.reload({container: '#task-widget', async: false});
                                                             $(this).modal('toggle');
                                                        }
                                                       
                                                    }
                                            "),
        lo\widgets\modal\ModalAjax::EVENT_MODAL_SHOW => new \yii\web\JsExpression("
            function(event, data, status, xhr, selector) {
                if (selector.hasClass('lo-modal-task-edit'))
                    $('#task-ajax .modal-header>span').html('<h4 class=\"modal-title\">Редактировать задачу</h4>');
                 else   
                $('#task-ajax .modal-header>span').html('<h4 class=\"modal-title\">Создать задачу</h4>');
            }
       "),],
    'url' => yii\helpers\Url::to(['/task/task-ajax']),
    'ajaxSubmit' => true,
]);
?>
<?php echo Html::a('<span class="pluso">+</span>Добавить', ['/task/create', 'id_issue' => $id_issue], ['class' => 'btn btn-success lo-modal-task float-right']); ?>

<div class="clearfix"></div>
<div class="box-typical p-a p-t-0">
    <div class="task-index">

        <?php Pjax::begin(['enablePushState' => true, 'timeout' => 2000, 'id' => 'task-widget']); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'tbl-typical'],
            'layout' => "{items}",
            // 'filterModel' => new TaskSearch(),
            'columns' => [
                [
                    'attribute' => 'title',
                    'format' => 'html',
                    'label' => 'Задача',
                    'content' => function ($data) {
                        return Html::a('<span class="table-title">' . $data->title . '</span>', ['/task/view', 'id' => $data->id], ['class' => 'black-link']) . '<br> <div class="">' .
                            '<a class="grey-text collapsed collapse-link" data-toggle="collapse" href="#collapse-desc-' . $data->id . '" >
    Детали
  </a>' . '<div class="collapse" id="collapse-desc-' . $data->id . '">' . $data->description . '</div></div>';
                    },
                    'enableSorting' => false
                ],
                [
                    'attribute' => 'Assigns',
                    'format' => 'html',
                    'label' => 'Исполнитель',
                    'content' => function ($data) {
                        $out = '';
                        foreach ($data->assigns as $assign) {
                            $out .= ' <div class="user-card-row">
                            <div class="tbl-row">
                                <div class="tbl-cell tbl-cell-photo">
                                    <a href="#">
                                        <img src="' . $assign->getImageSrc("small_") . '" alt="">
                                    </a>
                                </div>
                                <div class="tbl-cell">
                                    <p class="user-card-row-name"><a href="#">' . $assign->username . '</a></p>
                                    <p class="color-blue-grey-lighter">' . $assign->company_posiotion . '</p>
                                </div>
                            </div>
                        </div>';
                        }
                        return $out;
                    },
                    'enableSorting' => false
                ],
                [
                    'attribute' => 'deadline_local',
                    'format' => 'html',
                    'content' => function ($data) {
                        return $data->deadline_local . '<br>' . $data->statuslabel;
                    },
                    'enableSorting' => false
                ],

                ['class' => 'yii\grid\ActionColumn',
                    'controller' => '/task',
                    'template' => '{update}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            if ($model->accessEdit)
                                return Html::a('<span class="glyphicon glyphicon-pencil "></span>', ['/task/update', 'id' => $model->id, 'lomodal' => true], ['class' => 'lo-modal-task lo-modal-task-edit grey-text']);
                            else
                                return '';
                        },
                    ],
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>