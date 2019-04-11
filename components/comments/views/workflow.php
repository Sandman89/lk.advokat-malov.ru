<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use bs\Flatpickr\FlatpickrWidget;
use kartik\typeahead\Typeahead;
use kartik\file\FileInput;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var app\components\comments\models\CommentModel $commentModel
 */

$this->title = 'Создать этап дела';

?>


<div class="">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
        'fieldConfig' => [

            'options' => ['class' => 'form-group row'],
            'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper">{input}{error}</div></div>',
            'labelOptions' => ['class' => 'form-label'],
            'inputOptions' => ['autocomplete' => 'off', 'class' => 'form-control'],
            'errorOptions' => ['class' => 'form-tooltip-error']
        ],
    ]); ?>
    <div class="row">
        <div class="col-sm-6"> <?= $form->field($commentModel, 'createdAt_local')->widget(FlatpickrWidget::class, [
                'locale' => 'ru',
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
            ]); ?></div>
        <div class="col-sm-6">
            <?= $form->field($commentModel, 'title')->widget(Typeahead::classname(), [
                'options' => ['placeholder' => 'Выберите название или введите свое...','autocomplete' => 'off'],
                'defaultSuggestions' => \app\components\comments\models\CommentModel::$workflow_title,
                'pluginOptions' => ['highlight' => true],
                'dataset' => [
                    [
                        'local' => \app\components\comments\models\CommentModel::$workflow_title,
                        'limit' => 17
                    ]
                ]
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12"><?= $form->field($commentModel, 'content')->textarea(['rows' => 3]) ?></div>
    </div>
    <?= $form->field($commentModel, 'token_comment', ['template' => '{input}', 'options' => ['class' => ''],])->hiddenInput()->label(false); ?>

    <div class="row">
        <div class="col-sm-12">
            <?php echo FileInput::widget([
                'model' => $commentModel,
                'attribute' => 'image[]',
                'options' => ['multiple' => true],
                'pluginOptions' => [
                    'fileActionSettings' => [
                        'showZoom' => true,
                        'showRemove' => true,
                        'showUpload' => false,
                        'showDrag' => false,
                    ],
                    'layoutTemplates' => [
                        'progress' => '',
                        'size' => '',
                        'close' => '',
                        // 'footer'=>'<div class="file-thumbnail-footer"><div class="file-caption-name">{caption}{size}</div>{CUSTOM_TAG_NEW}{CUSTOM_TAG_INIT}{actions}</div>',
                    ],
                    'uploadAsync' => true,
                    //'showPreview' => true,
                    'showCaption' => false,
                    'showRemove' => false,
                    //   'showBrowse'=>false,
                    'browseOnZoneClick' => true,
                    'showCancel' => false,
                    'showUpload' => false,
                    'showUploadStats' => false,
                    'showBrowse' => false,
                    'previewClass'=>'workflow-file-preview',
                    'allowedFileExtensions' => ['jpg', 'gif', 'png', 'doc', 'docx', 'pdf', 'xlsx', 'rar', 'zip', 'xlsx', 'xls', 'txt', 'csv', 'rtf', 'one', 'pptx', 'ppsx', 'pot'],
                    'previewFileType' => 'image',
                    'previewFileIcon' => '<i class="font-icon font-icon-page"></i>',
                    'previewFileIconSettings' => [
                        'doc' => '<i class="font-icon font-icon-page"></i>',
                        'docx' => '<i class="font-icon font-icon-page"></i>',
                        'xls' => '<i class="font-icon font-icon-page"></i>',
                        'xlsx' => '<i class="font-icon font-icon-page"></i>',
                        'jpg' => '<i class="font-icon font-icon-picture-2"></i>',
                    ],
                    'previewSettings' => [
                        'doc' => '{width: "auto", height: "auto", max-width: "100%", max-height: "100%"}',
                        'docx' => '{width: "120px", height: "120px", max-width: "100%", max-height: "100%"}',
                        'xls' => '{width: "auto", height: "auto", max-width: "100%", max-height: "100%"}',
                        'xlsx' => '{width: "auto", height: "auto", max-width: "100%", max-height: "100%"}',
                        'jpg' => '{width: "auto", height: "auto", max-width: "100%", max-height: "100%"}',
                    ],
                    'preferIconicPreview' => true,
                    'allowedPreviewTypes' => null,
                    'initialPreviewAsData' => true,
                    'overwriteInitial' => false,
                    'browseLabel' => 'Выберите файлы',
                    'uploadExtraData' => [
                        'Filemanager[id_issue]' => $id_issue,
                        'Filemanager[token_comment]' => $token_comment,
                        'is_post' => $commentModel->isNewRecord ? 'new' : 'update'
                    ],
                    'msgPlaceholder' => 'Select attachments',
                    'maxFileCount' => 10,
                    'uploadUrl' => Url::to(['comment/upload']),
                ],
                'pluginEvents' => [
                    'filebatchselected' => 'function(event, files) {
             $(this).fileinput("upload");
             }',

                    'filepredelete' => 'function(event, files) {
                //var abort = true;
                var index = uploaded_images.indexOf(files);
                if (index !== -1) uploaded_images.splice(index, 1);
                 console.log(uploaded_images);
                 $("#productsmaster-images_array").val(uploaded_images);
               //return abort;   
           }',
                    'fileuploaded' => 'function(event, data, previewId, index){
             // alert( data.response.initialPreviewConfig[0].caption);
             // uploaded_images.push(data.response.initialPreviewConfig[0].key);
            //    console.log(uploaded_images);
            //    $("#productsmaster-images_array").val(uploaded_images);
              }',
                ]
            ]); ?>
        </div>
    </div>


    <div class="form-group row">
        <div class="col-sm-12 text-center">
            <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?><br>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <?php
    $script = <<< JS
   // initialize array    
   var uploaded_images = [];  
JS;
    $this->registerJs($script);
    ?>


