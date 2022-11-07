<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this \yii\web\View */
/* @var $commentModel \yii2mod\comments\models\CommentModel */
/* @var $encryptedEntity string */
/* @var $class_form string */
/* @var $formId string comment form id */
?>
<div class="comment-form-container">
    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => $formId,
            'class' => $class_form,
        ],
        'fieldConfig' => [
            'options' => ['class' => 'form-group row'],
            'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper">{input}{error}</div></div>',
            'labelOptions' => ['class' => 'form-label'],
            'errorOptions' => ['class' => 'form-tooltip-error']
        ],
        'action' => Url::to(['/comment/create', 'entity' => $encryptedEntity]),
        'validateOnChange' => false,
        'validateOnBlur' => false,
    ]); ?>
    <div>
    <?php echo $form->field($commentModel, 'content', [ 'template' => '<div class="col-sm-12"><div class="form-control-wrapper">{input}{error}</div></div>'])->textarea(['placeholder' => Yii::t('yii2mod.comments', 'Add a comment...'), 'rows' => 2, 'data' => ['comment' => 'content'],'tabindex' => '1']) ?>


    <?php echo $form->field($commentModel, 'parentId', ['options' => ['class' => ''], 'template' => '{input}'])->hiddenInput(['data' => ['comment' => 'parent-id']]); ?>
    <?= $form->field($commentModel, 'token_comment', ['template' => '{input}', 'options' => ['class' => ''],])->hiddenInput(['value' => $token_comment])->label(false); ?>
    <div class="comment-box-partial">
        <div class="button-container show">
            <?php echo FileInput::widget([
                'model' => $commentModel,
                'attribute' => 'image[]',
                'options' => ['multiple' => true,],
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
                    'dropZoneEnabled' => false,
                    'browseOnZoneClick' => false,
                    'showCancel' => false,
                    'showUpload' => false,
                    'showUploadStats' => false,
                    'showBrowse' => true,
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
                    'initialPreviewAsData' => true,
                    'overwriteInitial' => false,
                    'browseLabel' => '',
                    'browseIcon' => ' <i class="font-icon font-icon-clip color-blue-grey-lighter"></i>',
                    'browseClass' => '',
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
            ])  ?>
            <?php echo Html::submitButton('Опубликовать', ['class' => 'btn btn-success','tabindex'=>3]); ?>
            <?php echo Html::a('Отменить ответ', '#', ['id' => 'cancel-reply', 'class' => 'btn btn btn-default-outline pull-right', 'data' => ['action' => 'cancel-reply']]); ?>
            </div>
        </div>
    </div>
    <?php $form->end(); ?>
    <div class="clearfix"></div>
</div>
<?php
$script = new \yii\web\JsExpression("$('#".$formId." textarea').keydown(function (e) {
  if (e.ctrlKey && e.keyCode == 13) {
     $('#".$formId."').submit();
  }
});");
$this->registerJs($script, \yii\web\View::POS_END);
?>