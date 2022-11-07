<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\ckeditor\CKEditor;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Task */
/* @var $form yii\widgets\ActiveForm */
/* @var $hidden_id_issue bool */
?>

<?php $Flatpickr_options = [
    'locale' => 'ru',
    'plugins' => [
        'confirmDate' => [
            'confirmText' => 'OK',
            'showAlways' => false,
            'theme' => 'light',
        ],
    ],
    'options' => [
        'class' => 'form-control',
        'autocomplete' => 'off'
    ],
    'groupBtnShow' => true,
    'clientOptions' => [
        'allowInput' => true,
        'dateFormat' => "d.m.Y H:i",
        'defaultDate' => null,
        'enableTime' => true,
        'time_24hr' => true,
    ],
];
?>
<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user'),]) ?>

    <div class="task-form">

        <?php $form = ActiveForm::begin(
            ['id' => 'form-task',
                'enableAjaxValidation' => true,
                'enableClientValidation' => false,
                'validationUrl' => \yii\helpers\Url::toRoute('task/validation'),
                'fieldConfig' => [
                    'inputOptions' => ['autocomplete' => 'off', 'class' => 'form-control'],
                    'options' => ['class' => 'form-group row'],
                    'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper">{input}{error}</div></div>',
                    'labelOptions' => ['class' => 'form-label'],
                    'errorOptions' => ['class' => 'form-tooltip-error']
                ],
            ]);
        ?>
        <div class="box-typical box-typical-padding">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

            <section class="tabs-section" style="margin-bottom: 0">

                <ul class="nav nav-pills" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link <?= ($model->typetask == $model::TYPETASK_DUE) ? 'active':'' ?>" href="#tabs-4-tab-1" role="tab" data-toggle="tab"
                           aria-expanded="true">
                            С крайним сроком
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($model->typetask == $model::TYPETASK_BETWEEN_DATE) ? 'active':'' ?>" href="#tabs-4-tab-2" role="tab" data-toggle="tab" aria-expanded="false">
                            С датами начала и конца
                        </a>
                    </li>

                </ul>


                <div class="tab-content simple-tab-content">
                    <div role="tabpanel" class="tab-pane fade  <?= ($model->typetask == $model::TYPETASK_DUE) ? 'active in show ':'' ?> " id="tabs-4-tab-1" aria-expanded="true">
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'deadline_local')->widget(\bs\Flatpickr\FlatpickrWidget::className(), $Flatpickr_options); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="alert alert-blue-dirty alert-no-border alert-txt-colored alert-dismissible"
                                     role="alert" style="    font-size: 0.875rem;margin-top: -10px;">
                                    <strong> Уведомления:</strong>
                                    <ul>
                                        <li>Исполнителям за час до крайнего срока, если задача не решена;</li>
                                        <li>Постановщику по истечении крайнего срока.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div><!--.tab-pane-->
                    <div role="tabpanel" class="tab-pane fade <?= ($model->typetask == $model::TYPETASK_BETWEEN_DATE) ? 'active in show':'' ?>" id="tabs-4-tab-2" aria-expanded="false">
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'start_local')->widget(\bs\Flatpickr\FlatpickrWidget::className(), $Flatpickr_options); ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($model, 'end_local')->widget(\bs\Flatpickr\FlatpickrWidget::className(), $Flatpickr_options); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="alert alert-blue-dirty alert-no-border alert-txt-colored alert-dismissible"
                                     role="alert" style="    font-size: 0.875rem;margin-top: -10px;">
                                    <strong>Уведомления:</strong>
                                    <ul>
                                        <li>Исполнителям за час до начала задачи.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div><!--.tab-pane-->

                </div><!--.tab-content-->
            </section>

            <div class="row">

                <div class="col-sm-12">
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


            <?= $form->field($model, 'description', ['options' => ['class' => 'form-group row'],
                'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper cke-wrapper">{input}{error}</div></div>'])->widget(CKEditor::className(),
                [
                    'preset' => 'custom',
                    'options' => ['rows' => 3],
                    'clientOptions' => [
                        'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper cke-wrapper">{input}{error}</div></div>',
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
                ]); ?>
            <?php
            // Связь между задачей и делом
            $issue_title = empty($model->id_issue) ? '' : \app\models\Issues::findOne($model->id_issue)->title;
            $hidden_id_issue = ($hidden_id_issue) ? ' hidden' : ''; ?>
            <?= $form->field($model, 'id_issue', [
                'options' => ['class' => 'form-group row ' . $hidden_id_issue],
                'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper custom-select2">{input}{error}</div></div>',
                'errorOptions' => ['class' => 'form-tooltip-error'],
                'labelOptions' => ['class' => 'form-label']])->widget(Select2::classname(), [

                'initValueText' => $issue_title,
                'bsVersion' => 4,
                'language' => 'ru',
                'options' => ['placeholder' => 'Установите связь с выбранным делом...',],
                'size' => Select2::SMALL,
                'pluginOptions' => [
                    'minimumInputLength' => 2,
                    'allowClear' => true,
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['issues/issue-list']),
                        'dataType' => 'json',
                        'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                    ],
                ],
                'addon' => [
                    'prepend' => [
                        'content' => '<span class="glyphicon glyphicon-briefcase"></span>',
                    ],
                ],
            ]); ?>


            <input type="text" class="btn btn-success" id="button-state" name="button-state" hidden value="">

            <div class="form-group-buttons row">
                <div class="col-sm-12 text-center">
                    <?php if ($model->isNewRecord)
                        echo Html::submitButton('Создать', ['class' => 'btn btn-success ']);
                    else {
                        //if ($hidden_id_issue)
                            echo Html::submitButton('Обновить', ['class' => 'btn btn-success ']);
                      /*  else {
                            echo Html::submitButton('Обновить', ['class' => 'btn btn-success ',
                                'name' => 'create',
                                'form' => 'form-task',
                                'onclick' => 'handleSubmitButton(this)',]);
                            echo " ";
                            echo Html::submitButton('Обновить и перейти к комментариям', ['class' => 'btn btn-success',
                                'name' => 'go',
                                'form' => 'form-task',
                                'onclick' => 'handleSubmitButton(this)',]);
                        }*/
                    }
                    ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php

$script = new \yii\web\JsExpression("
    function handleSubmitButton(_button) {
       jQuery('#button-state').val(_button.name);
    }
");
$this->registerJs($script, \yii\web\View::POS_END);
?>