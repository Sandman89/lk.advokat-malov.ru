<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use yii\helpers\ArrayHelper;
use kartik\tree\TreeViewInput;
use kartik\select2\Select2;


/* @var $this yii\web\View */
/* @var $model app\models\Issues */
/* @var $form yii\widgets\ActiveForm */
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user'),]) ?>
<div class="issues-form">

    <?php $form = ActiveForm::begin(
        ['id' => 'form-issues',
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off', 'class' => 'form-control'],
            ],
        ]);
    ?>
    <div class="box-typical box-typical-padding">
        <h5 class="m-b-lg">Информация по делу</h5>
        <?= $form->field($model, 'title', [
            'options' => ['class' => 'form-group row'],
            'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper">{input}{error}</div></div>',
            'errorOptions' => ['class' => 'form-tooltip-error'],
            'labelOptions' => ['class' => 'form-label']])->textInput() ?>

        <?= $form->field($model, 'description', [
            'options' => ['class' => 'form-group row'],
            'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper cke-wrapper">{input}{error}</div></div>',
            'errorOptions' => ['class' => 'form-tooltip-error'],
            'labelOptions' => ['class' => 'form-label']])->widget(CKEditor::className(),
            [
                'preset' => 'custom',
                'options' => ['rows' => 3],
                'clientOptions' => [
                    'removePlugins' => 'elementspath',
                    'extraPlugins' => 'colorbutton,justify',
                    'resize_enabled' => false,
                    'height' => 120,
                    'removeButtons' => 'Styles,Anchor,Subscript,Superscript,Flash,Smiley,SpecialChar,PageBreak,Iframe,HorizontalRule,Table',
                    'toolbarGroups' => [
                        ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
                        ['name' => 'paragraph', 'groups' => ['list', 'align']],
                        ['name' => 'colors'],
                        ['name' => 'links', 'groups' => ['links', 'insert']],
                        ['name' => 'styles'],

                    ],
                ]
            ])
        ?>
        <?= $form->field($model, 'id_category', [
            'options' => ['class' => 'form-group row'],
            'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper">{input}{error}</div></div>',
            'errorOptions' => ['class' => 'form-tooltip-error'],
            'labelOptions' => ['class' => 'form-label']])->widget(TreeViewInput::classname(), [
            'bsVersion' => 3,
            'query' => app\models\Tree::find()->addOrderBy('root, lft'),
            'headingOptions' => ['label' => 'Выберите нужную категорию'],
            'rootOptions' => ['label' => '<span class="text-primary">Категории судебной практики</span>'],
            'name' => 'id_category',    // input name
            'value' => $model->id_category,         // values selected (comma separated for multiple select)
            'asDropdown' => true,            // will render the tree input widget as a dropdown.
            'multiple' => false,            // set to false if you do not need multiple selection
            'fontAwesome' => false,            // render font awesome icons
            // custom root label
            'options' => ['id' => 'input-id_category'],
        ]); ?>
        <?= $form->field($model, 'contract_number', [
            'options' => ['class' => 'form-group row'],
            'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper">{input}{error}</div></div>',
            'errorOptions' => ['class' => 'form-tooltip-error'],
            'labelOptions' => ['class' => 'form-label']])->textInput() ?>
    </div>
    <div class="box-typical box-typical-padding">
        <div class="box-typical-wrapper">


            <div class="row">
                <div class="col-sm-6">

                    <h5 class="m-b-lg">Информация о клиенте</h5>
                    <?php
                    $client_username = empty($model->id_client) ? '' : $model->client->username;
                    echo $form->field($model, 'id_client', [
                        'options' => ['class' => 'form-group row'],
                        'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper custom-select2">{input}{error}</div></div>',
                        'errorOptions' => ['class' => 'form-tooltip-error'],
                        'labelOptions' => ['class' => 'form-label']])->widget(Select2::classname(), [
                        'initValueText' => $client_username,
                        'bsVersion' => 4,
                        'language' => 'ru',
                        'options' => ['placeholder' => 'Выберите клиента...',],
                        'size' => Select2::SMALL,
                        'pluginOptions' => [
                            'minimumInputLength' => 2,
                            'allowClear' => true,
                            'dropdownParent' =>  (Yii::$app->request->isAjax) ? new \yii\web\JsExpression('$("#issue-ajax")') : false,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['admin/client-list']),
                                'dataType' => 'json',
                                'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                            ],
                        ],
                        'addon' => [
                            'prepend' => [
                                'content' => '<span class="font-icon glyphicon glyphicon-user"></span>',
                            ],
                            'append' => [
                                'content' => lo\widgets\modal\ModalAjax::widget([
                                    'id' => 'user-ajax',
                                    'header' => '<h4 class="modal-title">Создать нового клиента</h4>',
                                    'toggleButton' => [
                                        'label' => 'Создать',
                                        'class' => 'btn btn-default-outline'
                                    ],
                                    'closeButton' => [
                                        'class' => 'close modal-close'
                                    ],
                                    'autoClose' => false,
                                    //событие
                                    'events' => [
                                        lo\widgets\modal\ModalAjax::EVENT_MODAL_SUBMIT => new \yii\web\JsExpression("
                                                function(event, data, status, xhr, selector) {
                                                    if(status){
                                                            user = JSON.parse(data);
                                                            $('#issues-id_client').append($('<option>', {
                                                                value: user.id,
                                                                text: user.username
                                                            }));
                                                            $('#issues-id_client').val(user.id).trigger('change');
                                                            $(this).modal('toggle');                                                            
                                                        }
                                                       
                                                    }
                                            "),],
                                    'url' => yii\helpers\Url::to(['/admin/create','role'=>'client']),
                                    'ajaxSubmit' => true,
                                ]),
                                'asButton' => true
                            ]

                        ],
                    ]); ?>

                </div>
                <div class="col-sm-6">

                    <h5 class="m-b-lg">Ответственная сторона</h5>
                    <?
                    $formatJs = <<< 'JS'
var formatRepo = function (repo) {
    if (repo.loading) {
        return repo.text;
    }
    var markup = '<img class="item-select-user_image" src="' + repo.element.dataset.image + '"/>' + repo.text;
    return '<div class="item-select-user" style="overflow:hidden;">' + markup + '</div>';
};
var formatRepoSelection = function (repo) {
var markup = '<img class="item-select-user_image" src="' + repo.element.dataset.image + '"/>' + repo.text;
   return '<div class="item-select-user" style="overflow:hidden;">' + markup + '</div>';
}
JS;
                    // Register the formatting script
                    $this->registerJs($formatJs, \yii\web\View::POS_HEAD);
                    //формириуем массив для data-image в тегах option у select input
                    $optionDataAttributes = ArrayHelper::map(\dektrium\user\models\User::find()->where(['role' => 'expert'])->all(), 'id', function ($model) {
                        return ['data-image' => $model->getImageSrc('small_')];
                    });
                    echo $form->field($model, 'assign_id', [
                        'options' => ['class' => 'form-group row'
                        ],
                        'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper custom-select2">{input}{error}</div></div>',
                        'errorOptions' => ['class' => 'form-tooltip-error'],
                        'labelOptions' => ['class' => 'form-label']])->widget(Select2::classname(), [

                        'data' => ArrayHelper::map(\dektrium\user\models\User::find()->where(['role' => 'expert'])->all(), 'id', 'username'),
                        'size' => Select2::SMALL,
                        'bsVersion' => 4,
                        'language' => 'ru',
                        'showToggleAll' => false,
                        'options' => ['placeholder' => 'Выберите исполнителя...', 'multiple' => true, 'options' => $optionDataAttributes],
                        'pluginOptions' => [
                            'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new \yii\web\JsExpression('formatRepo'),
                            'templateSelection' => new \yii\web\JsExpression('formatRepoSelection'),
                            'allowClear' => true
                        ],
                        'addon' => [
                            'prepend' => [
                                'content' => '<span class="font-icon glyphicon glyphicon-user"></span>',
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>


    <div class="box-typical box-typical-padding">
        <a class="grey-text  <?=  (($model->court_date) && ($model->court)) ? '':'collapsed' ?> collapse-link" data-toggle="collapse" href="#sud-info"><h5 class="m-b-lg">
                Информация о суде</h5></a>
        <div class="collapse <?=  (($model->court_date) && ($model->court)) ? 'show':'' ?>" id="sud-info">
            <?= $form->field($model, 'court', ['options' => ['class' => 'form-group row'],
                'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper">{input}{error}</div></div>',
                'errorOptions' => ['class' => 'form-tooltip-error'],
                'labelOptions' => ['class' => 'form-label']])->textInput() ?>
            <?= $form->field($model, 'judge', ['options' => ['class' => 'form-group row'],
                'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper">{input}{error}</div></div>',
                'errorOptions' => ['class' => 'form-tooltip-error'],
                'labelOptions' => ['class' => 'form-label']])->textInput() ?>

            <?= $form->field($model, 'court_date_local')->widget(\bs\Flatpickr\FlatpickrWidget::className(), [
                'locale' => strtolower(substr(Yii::$app->language, 0, 2)),
                // https://chmln.github.io/flatpickr/plugins/
                'plugins' => [
                    'confirmDate' => [
                        'confirmText' => 'OK',
                        'showAlways' => false,
                        'theme' => 'light',
                    ],
                ],
                'groupBtnShow' => true,
                'options' => [
                    'class' => 'form-control',
                    'autocomplete' => 'off'
                ],
                'clientOptions' => [
                    // config options https://chmln.github.io/flatpickr/options/
                    'allowInput' => true,
                    'dateFormat' => "d.m.Y H:i",
                    'defaultDate' => null,
                    'enableTime' => true,
                    'time_24hr' => true,
                ],
            ]); ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>
    <div class="form-group-buttons row">
        <div class="col-sm-12 text-center">
            <?= Html::submitButton(($model->isNewRecord) ? 'Создать' : 'Обновить', ['class' => 'btn btn-success ',
                'name' => 'create',
                'form' => 'form-issues'
            ]) ?>

        </div>
    </div>
</div>

<?php

$script = new \yii\web\JsExpression("
   jQuery('#input-id_category').on('treeview.checked', function(event, key) {
             jQuery('.kv-tree-dropdown-container').removeClass('show');
                    jQuery('.kv-tree-dropdown').removeClass('show');
                    jQuery('.kv-tree-dropdown').attr(\"style\",\"\");
    });
    $('#user-ajax').on('hidden.bs.modal', function (e) {
        if ($('#issue-ajax').length > 0)
            $('body').addClass('modal-open');
    })
");
$this->registerJs($script, \yii\web\View::POS_READY);
?>
