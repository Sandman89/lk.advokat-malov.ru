<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use lo\widgets\modal\ModalAjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\IssuesSearch */
/* @var bool $is_archive true - если вызван из экшена АрхивЗадач */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ($is_archive)
    $this->title = 'Архив моих дел';
else
    $this->title = 'Дела';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php echo lo\widgets\modal\ModalAjax::widget([
    'id' => 'issue-ajax',
    'header' => 'Создать дело',
    'closeButton' => [
        'class' => 'close modal-close'
    ],
    'selector' => 'a.lo-modal-issue',
    'autoClose' => true,
    'pjaxContainer' => '#issues-pjax',
    //событие
    'events' => [
        lo\widgets\modal\ModalAjax::EVENT_MODAL_SUBMIT => new \yii\web\JsExpression("
                                                function(event, data, status, xhr, selector) {
                                                    if(status){
                                                             $.pjax.reload({container: '#issues-pjax', async: false});
                                                             $(this).modal('toggle');
                                                             $(this).modal('handleUpdate');
                                                        }
                                                       
                                                    }
                                           ")],
    'ajaxSubmit' => true,
]);
?>
    <div class="issues-index">

        <div class="row">
            <div class="col-sm-8">
                <header class="section-header ">
                    <h3 class=""><?= Html::encode($this->title) ?> </h3><i
                            class="font-icon font-icon-question grey-text m-l" data-toggle="tooltip"
                            data-trigger="hover"
                            data-placement="right"
                            title="<strong>Дело</strong> - это набор связанных атрибутов, которые отражают рабочий процесс по этому делу. <br>Дело должно быть связано с клиентом, и тогда у клиента будет доступ к этому делу со своего аккаунта. <br>Клиент может получать уведомления об этапах выполнения дела, а также коммуницировать внутри своего дела."></i>
                </header>
            </div>
            <div class="col-sm-4 text-right">
                <?php echo Html::a('<span class="pluso">+</span>Создать дело', ['issues/create'], ['class' => 'btn btn-success lo-modal-issue float-right', 'title' => '<h4 class="modal-title">Создать дело</h4>']); ?>
            </div>
        </div>


        <?php Pjax::begin(['id' => 'issues-pjax', 'timeout' => 2000, 'enablePushState' => false]); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <div class="box-typical">

            <?= GridView::widget([
                'id' => 'issues-grid',
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
                            $out = Html::a('<span class="table-title m-r-min">' . $data->title . '</span>', ['issues/view', 'id' => $data->id], ['class' => 'black-link']);
                            $out .= '<span>'.$data->statuslabel.'</span>';
                            $out .= ($data->id_category) ? '<div class="grey-text  font-14"><i class="font-icon font-icon-folder m-r-min"></i> ' . $data->category->name . '</div>' : '';
                            $out .= ($data->created_at) ? '<div class="grey-text  font-14"><i class="glyphicon glyphicon-calendar m-r-min"></i> ' . Yii::$app->formatter->asDatetime($data->created_at, 'php:d.m.Y в H:i') . '</div>' : '';
                            if (!empty($data->description))
                                $out .= ' <div class="collapse-block-table"><a class="grey-text collapsed collapse-link" data-toggle="collapse" href="#collapse-desc-' . $data->id . '" >Детали</a>' .
                                    '<div class="collapse" id="collapse-desc-' . $data->id . '">' . $data->description . '</div></div>';
                            return $out;
                        },
                    ],
                    /*[
                        'attribute' => 'created_at',
                        'label' => 'Дата создания',
                        'value' => function ($model) {
                            if (extension_loaded('intl')) {
                                return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at]);
                            } else {
                                return date('Y-m-d G:i:s', $model->created_at);
                            }
                        },
                    ],*/
                    [
                        'attribute' => 'workflow',
                        'format' => 'html',
                        'label' => 'Исполнение дела',
                        'filterInputOptions' => [
                            'placeholder' => 'поиск...',
                            'class' => 'form-control',
                        ],
                        'content' => function ($data) {
                            $out = '';
                            if (!empty($data->workflows)) {
                                foreach ($data->workflows as $workflow) {
                                    $out .= '<div>' . $workflow->title . '<div class="grey-text  font-14"><i class="glyphicon glyphicon-calendar m-r-min"></i> ' . Yii::$app->formatter->asDatetime($workflow->createdAt, 'php:d.m.Y в H:i') . '</div></div>';
                                }
                            }
                            return $out;
                        },
                    ],
                    [
                        'attribute' => 'client',
                        'format' => 'html',
                        'label' => 'Клиент',
                        'filterInputOptions' => [
                            'placeholder' => 'поиск...',
                            'class' => 'form-control',
                        ],
                        'content' => function ($data) {
                            $out = '';
                            if (!empty($data->client)) {
                                $out .= '<div class="user-card-row">
                            <div class="tbl-row">
                                <div class="tbl-cell">
                                    <p class="user-card-row-name"><a href="#">' . $data->client->username . '</a></p>';
                                if (!empty($data->client->phone))
                                    $out .= '<p class="grey-text  font-14"><i class="grey-text glyphicon glyphicon-earphone m-r-min font-14"></i> ' . $data->client->phone . '</p>';
                                $out .= '<p class="grey-text  font-14"><i class="grey-text font-icon font-icon font-icon-mail m-r-min font-14"></i> ' . $data->client->email . '</p>
                                </div>
                            </div>
                        </div>';
                            }
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
                    [
                        'attribute' => 'court_date',
                        'format' => 'html',
                        'label' => 'Дата суда',
                        'filter' => \bs\Flatpickr\FlatpickrWidget::widget([
                            'model' => $searchModel,
                            'attribute' => 'court_date',
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
                        'content' => function ($data) {
                            $out = '';
                            if (($data->court_date) && ($data->court))
                                $out .= '<div >' . $data->court_date_local . '</div>
                                    <div class="grey-text  font-14">Суд: ' . $data->court . '</div>
                                    <div class="grey-text  font-14">Cудья: ' . $data->judge . '</div>';
                            return $out;
                        },
                    ],
                    ['attribute' => 'contract_number',
                        'filterInputOptions' => [
                            'placeholder' => 'поиск...',
                            'class' => 'form-control',
                        ],
                    ],
                    ['class' => 'yii\grid\ActionColumn',
                        'controller' => '/issues',
                        'template' => '{update}{complete}{restore}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                if ($model->accessEdit)
                                    return Html::a('<span class="glyphicon glyphicon-pencil "></span>', ['/issues/update', 'id' => $model->id, 'lomodal' => true], ['class' => 'block lo-modal-issue lo-modal-issue-edit grey-text', 'title' => '<h4 class="modal-title">Редактировать дело</h4>']);
                                else
                                    return '';
                            },
                            'complete' => function ($url, $model) {
                                if ($model->accessComplete)
                                    return Html::a('<span class="font-icon font-icon-archive "></span>', ['/issues/complete', 'id' => $model->id, 'lomodal' => true], ['class' => 'block grey-text', 'data-pjax' => '0', 'data-confirm' => "Вы уверены, что хотите закрыть дело и перенести его архив?", 'title' => 'Завершить и перенести в архив']);
                                else
                                    return '';
                            },
                            'restore' => function ($url, $model) {
                                if ($model->accessRestore)
                                    return Html::a('<span class="glyphicon glyphicon-share "></span>', ['/issues/restore', 'id' => $model->id,'lomodal' => true], ['class' => 'block grey-text',   'title' => 'Восстановить дело из архива']);
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
            if (($('body .flatpickr-calendar').hasClass('open'))&&($('#issuessearch-court_date').val() != '')){
               // alert('открыта форма');
                return false;        
            }
            else{
                $('body .flatpickr-calendar').remove();
            }
            }
         return true;
    });

     $('body #issue-ajax').on('hidden.bs.modal', function (e) {
         $('#user-ajax').remove();
         $('.flatpickr-calendar.hasTime').remove();
    })
");
$this->registerJs($script, \yii\web\View::POS_READY);
?>