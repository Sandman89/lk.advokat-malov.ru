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
$is_workflow = (!empty($commentModel->title)) ? true : false;
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
    <?php if ($is_workflow) : ?>
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
                'options' => ['placeholder' => 'Выберите название или введите свое...', 'autocomplete' => 'off'],
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
    <?php endif; ?>
    <div class="row">
        <div class="col-sm-12"><?= $form->field($commentModel, 'content')->textarea(['rows' => 3]) ?></div>
    </div>
    <?= $form->field($commentModel, 'token_comment', ['template' => '{input}', 'options' => ['class' => ''],])->hiddenInput()->label(false); ?>
    <?php
    //For Update Form : Fetch Uploaded Files and create Array to preview
    $fileList = [];
    $fileListId = [];
    if (!empty($files))
        foreach ($files as $file) {
            $fileList[] = Url::base(TRUE) . $file->path;
            $fileListId[] = [
                'key' => $file->id,
                'caption' => $file->original_name . '.' . $file->ext,
                'type' => $file->type
            ];
        }
    ?>
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
                    'previewClass' => 'workflow-file-preview',
                    'allowedFileExtensions' => ['jpg', 'jpeg', 'gif', 'png', 'doc', 'docx', 'pdf', 'xlsx', 'rar', 'zip', 'xlsx', 'xls', 'txt', 'csv', 'rtf', 'one', 'pptx', 'ppsx', 'pot'],
                    'previewFileType' => 'image',
                    'previewFileIcon' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                    'previewFileIconSettings' => [
                        'doc' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                        'docx' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                        'xls' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                        'xlsx' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                        'rar' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                        'zip' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                        'txt' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                        'csv' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                        'rtf' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                        'one' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                        'pptx' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                        'ppsx' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                        'pot' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                        'pdf' => '<i class="font-icon font-icon-attachment-type font-icon-page"></i>',
                        'jpg' => '<i class="font-icon font-icon-attachment-type font-icon-picture-2"></i>',
                        'jpeg' => '<i class="font-icon font-icon-attachment-type font-icon-picture-2"></i>',
                        'png' => '<i class="font-icon font-icon-attachment-type font-icon-picture-2"></i>',
                    ],
                    'previewSettings' => [
                        "office" => ["width" => "auto", "height" => "auto"],
                        "text" => ["width" => "auto", "height" => "auto"],
                        "image" => ["width" => "auto", "height" => "auto"],
                        "pdf" => ["width" => "auto", "height" => "auto"],
                        "other" => ["width" => "auto", "height" => "auto"],
                    ],
                    'preferIconicPreview' => true,
                    'initialPreview' => $fileList,
                    'initialPreviewConfig' => $fileListId,
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
                    'deleteUrl' => Url::to(['comment/delete-file']),
                    'uploadUrl' => Url::to(['comment/upload']),
                ],
                'pluginEvents' => [
                    'filebatchselected' => 'function(event, files) {
             $(this).fileinput("upload");
             }',
                ]
            ]); ?>
        </div>
    </div>


    <div class="form-group row">
        <div class="col-sm-12 text-center">
            <?= Html::submitButton($commentModel->isNewRecord ? 'Создать' : 'Обновить', ['class' => 'btn btn-success']) ?>
            <br>
        </div>
    </div>

    <?php ActiveForm::end(); ?>


