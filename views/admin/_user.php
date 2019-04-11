<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;

/**
 * @var yii\widgets\ActiveForm $form
 * @var dektrium\user\models\User $user
 */
?>
<?php
    $isExpert = (((Yii::$app->controller->route == "admin/create-expert")||($user->isExpert))&&(!Yii::$app->request->isAjax)) ? true : false;
?>

<?= $form->field($user, 'email') ?>
<?= $form->field($user, 'username') ?>
<?= $form->field($user, 'password')->passwordInput() ?>
<?= $form->field($user, 'phone') ?>
<?php if (!$isExpert) {
    echo $form->field($user, 'card_number');
}
?>

<?php if ($isExpert): ?>
    <?= $form->field($user, 'company_posiotion') ?>
    <?php if (Yii::$app->user->identity->isAdmin)
        echo $form->field($user, 'admin', [
            'template' => '<div class="col-sm-12 checkbox">{input}{label}{error}<div><small class="text-muted">Администатор может видеть дела и задачи всех сотрудников. Специалисту доступны только дела и задачи, которые его касаются</small></div></div>',
            'labelOptions' => ['class' => 'form-label'],
            'inputOptions' => ['class' => 'checkbox']
        ])->checkbox([], false)->label();
    ?>
<?php endif ?>
<?= $form->field($user, 'image')->widget('demi\image\FormImageWidget', [
    'imageSrc' => $user->getImageSrc('small_'),
    'deleteUrl' => ['deleteImage', 'id' => $user->getPrimaryKey()],
    'cropUrl' => ['cropImage', 'id' => $user->getPrimaryKey()],
    // cropper options https://github.com/fengyuanchen/cropper/blob/master/README.md#options
    'cropPluginOptions' => [],
    // Translated messages
    'messages' => [
        // {formats} and {formattedSize} will replaced by widget to actual values
        'formats' => '',
        'fileSize' => '',
        'deleteBtn' => 'Удалить',
        'deleteConfirmation' => Yii::t('app', 'Are you sure you want to delete the image?'),
        // Cropper
        'cropBtn' => Yii::t('app', 'Обрезать'),
        'cropModalTitle' => Yii::t('app', 'Select crop area and click "Crop" button'),
        'closeModalBtn' => Yii::t('app', 'Close'),
        'cropModalBtn' => Yii::t('app', 'Crop selected'),
    ],
]) ?>

<?php
$script = new \yii\web\JsExpression("
   jQuery(':file').filestyle({dragdrop: false,text:'Выберите файл'});
");
$this->registerJs($script, \yii\web\View::POS_END);
?>
