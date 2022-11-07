<?php

use lo\widgets\modal\ModalAjax;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var bool $is_archive true - если вызван из экшена АрхивЗадач */
/* @var $searchModel app\models\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ($is_archive)
    $this->title = 'Архив моих задач';
else
    $this->title = 'Мои задачи';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php echo lo\widgets\modal\ModalAjax::widget([
    'id' => 'task-ajax',
    'header' => 'Создать задачу',
    'closeButton' => [
        'class' => 'close modal-close'
    ],
    'selector' => 'a.lo-modal-task',
    'autoClose' => true,
    'pjaxContainer' => '#task-pjax',
    //событие
    'events' => [
        lo\widgets\modal\ModalAjax::EVENT_MODAL_SUBMIT => new \yii\web\JsExpression("
                                                function(event, data, status, xhr, selector) {
                                                    if(status){
                                                             $.pjax.reload({container: '#task-pjax', async: false});
                                                             $(this).modal('toggle');
                                                             $(this).modal('handleUpdate');
                                                        }                                          
                                                    }
                                           ")],
    'ajaxSubmit' => true,
]);
?>
    <div class="task-index">

        <div class="row">
            <div class="col-sm-4">
                <header class="section-header">
                    <h3><?= Html::encode($this->title) ?> </h3>
                </header>
            </div>
            <div class="col-sm-8">
                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <?php echo Html::a('<span class="pluso">+</span>Создать задачу', ['task/create'], ['class' => 'btn btn-success lo-modal-task float-right', 'title' => '<h4 class="modal-title">Создать задачу</h4>']); ?>
                        <br>
                    </div>
                </div>
            </div>
        </div>


        <?php Pjax::begin(['id' => 'task-pjax', 'timeout' => 2000, 'enablePushState' => false]); ?>
        <?php echo $this->render('_search', ['model' => $searchModel,'is_archive'=>$is_archive]); ?>
        <div class="box-typical">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'tableOptions' => ['class' => 'tbl-typical tbl-typical-min'],
                'layout' => '{items}{pager}',
                'pager' => [
                    'linkOptions' => [
                        'class' => 'page-link'
                    ],
                    'disabledListItemSubTagOptions' => ['tag' => 'span', 'class' => 'page-link'],
                    'prevPageLabel' => 'prev',
                    'nextPageLabel' => 'next',
                    'pageCssClass' => 'paginate_button page-item',
                    'nextPageCssClass' => 'paginate_button page-item next',    // Set CSS class for the “next” page button
                    'prevPageCssClass' => 'paginate_button page-item previous',    // Set CSS class for the “previous” page button
                ],
                'columns' => [
                    [
                        'attribute' => 'title',
                        'filterInputOptions' => [
                            'placeholder' => 'поиск...',
                            'class' => 'form-control',
                        ],
                        'format' => 'html',
                        'label' => 'Краткое название',
                        'content' => function ($data) {
                            Yii::debug($data->issue);
                            $out = Html::a('<span class="table-title m-r-min">' . $data->title . '</span>', ['task/view', 'id' => $data->id], ['class' => 'black-link']);
                            $out .= '<span>'.$data->statuslabel.'</span>';
                            $out .= ($data->issue) ? '<div class="grey-text  font-14"><i class="glyphicon glyphicon-briefcase m-r-min"></i> ' . Html::a('<span class="table-title">' . $data->issue->title . '</span>', ['issues/view', 'id' => $data->issue->id], ['class' => 'grey-text']) . '</div>' : '';
                            if (!empty($data->description))
                                $out .= ' <div class="collapse-block-table"><a class="grey-text collapsed collapse-link" data-toggle="collapse" href="#collapse-desc-' . $data->id . '" >Детали</a>' .
                                    '<div class="collapse" id="collapse-desc-' . $data->id . '">' . $data->description . '</div></div>';

                            return $out;
                        },
                    ],
                    [
                        'attribute' => 'dates',
                        'filter' => \bs\Flatpickr\FlatpickrWidget::widget([
                            'model' => $searchModel,
                            'attribute' => 'dates',
                            'plugins' => [

                            ],
                            'options' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off',
                                'placeholder' => 'поиск...',
                                'data-pjax' => 0
                            ],
                            'clientOptions' => [
                                'mode' => "range",
                                'allowInput' => true,
                                'dateFormat' => "d.m.Y",
                                'defaultDate' => null,
                                'enableTime' => false,
                                'time_24hr' => true,
                            ],
                            'locale' => strtolower(substr(Yii::$app->language, 0, 2)),
                        ]),
                        'format' => 'html',
                        'label' => 'Сроки',
                        'content' => function ($data) {
                            $out = '';
                            if (($data->deadline_local))
                                $out .= '<div class=""><i class="font-icon  grey-text  font-14 font-icon-fire m-r-min" title="Крайний срок"></i> ' . $data->deadline_local . '</div>';
                            if (($data->start_local) && ($data->end_local))
                                $out .= '<div >' . $data->start_local . ' - ' . $data->end_local . '</div>';

                            return $out;
                        },
                    ],
                    [
                        'attribute' => 'author',
                        'filterInputOptions' => [
                            'placeholder' => 'поиск...',
                            'class' => 'form-control',
                        ],
                        'format' => 'html',
                        'label' => 'Постановщик',
                        'content' => function ($data) {
                            $out = ' <div class="user-card-row">
                            <div class="tbl-row">
                                <div class="tbl-cell tbl-cell-photo">
                                    <a href="#">
                                        <img src="' . $data->author->getImageSrc("small_") . '" alt="">
                                    </a>
                                </div>
                                <div class="tbl-cell">
                                    <p class="user-card-row-name"><a href="#">' . $data->author->username . '</a></p>
                                </div>
                            </div>
                        </div>';
                            return $out;
                        },
                    ],
                    [
                        'attribute' => 'assigns',
                        'format' => 'html',
                        'label' => 'Исполнитель',
                        'filterInputOptions' => [
                            'placeholder' => 'поиск...',
                            'class' => 'form-control',
                        ],
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
                                </div>
                            </div>
                        </div>';
                            }
                            return $out;
                        },
                    ],
                    ['attribute' => 'created_at',
                        'label' => 'Дата постановки',
                        'filterInputOptions' => [
                            'placeholder' => 'поиск...',
                            'class' => 'form-control',
                        ],
                        'content' => function ($data) {
                            return Yii::$app->formatter->asDatetime($data->created_at, 'php:d.m.Y в H:i');
                        },
                        'filter' => \bs\Flatpickr\FlatpickrWidget::widget([
                            'model' => $searchModel,
                            'attribute' => 'created_date_filter',
                            'options' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off',
                                'placeholder' => 'поиск...',
                                'data-pjax' => 0
                            ],
                            'clientOptions' => [
                                'mode' => "range",
                                'allowInput' => true,
                                'dateFormat' => "d.m.Y",
                                'defaultDate' => null,
                                'enableTime' => false,
                                'time_24hr' => true,
                            ],
                            'locale' => strtolower(substr(Yii::$app->language, 0, 2)),
                        ]),
                    ],


                    ['class' => 'yii\grid\ActionColumn',
                        'controller' => '/task',
                        'template' => '{update}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                if ($model->accessEdit)
                                    return Html::a('<span class="glyphicon glyphicon-pencil "></span>', ['/task/update', 'id' => $model->id, 'lomodal' => true], ['class' => 'block lo-modal-task lo-modal-task-edit grey-text', 'title' => '<h4 class="modal-title">Редактировать задачу</h4>']);
                                else
                                    return '';
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
        <?php Pjax::end(); ?>
    </div>
<?php
$script = new \yii\web\JsExpression("
 
   //нужно для фильтра по диапазону дат в js плагине flatpick. Дело в том, что flatpick как только выбрал первую дату он ее ставит в input и потом pjax сразу шлет запрос.
   //Поэтому если flatpick активен и в поле фильтра есть данные то не нужно перезагружать pjax. Только когда flatpick подставил оба диапазона тогда посылаем pjax.  
   $(document).on('pjax:beforeSend',  function (e, xhr, settings) {
        if ($('body .flatpickr-calendar').length > 0){
            if (($('body .flatpickr-calendar').hasClass('open'))&&(($('#tasksearch-dates').val() != '')||($('#tasksearch-created_date_filter').val() != ''))){
               // alert('открыта форма');
                return false;        
            }
            else{
                $('body .flatpickr-calendar').remove();
            }
            }
         return true;
    });

     $('body #task-ajax').on('hidden.bs.modal', function (e) {
         $('#user-ajax').remove();
         $('.flatpickr-calendar.hasTime').remove();
    })
");
$this->registerJs($script, \yii\web\View::POS_READY);
?>