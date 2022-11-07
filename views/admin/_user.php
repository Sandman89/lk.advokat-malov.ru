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
 * @var bool $role
 * @var dektrium\user\models\User $user
 */
?>
<?php
    $isExpert = ($role == 'expert') ? true : false;
?>

<?= $form->field($user, 'username') ?>
<?= $form->field($user, 'email') ?>
<?php if ($user->scenario != 'settings'): ?>
<?= $form->field($user, 'password')->passwordInput() ?>
<?php endif; ?>
<?php if ($user->scenario == 'settings'): ?>
    <div id="password_fields" style="display: none;">
        <?= $form->field($user, 'new_password')->passwordInput() ?>
        <?= $form->field($user, 'password')->passwordInput()->label('Текущий пароль')  ?>
    </div>


    <button type="button" class="btn btn-primary" style="margin-bottom: 20px"
                onclick="$(this).hide();$('#password_fields').slideDown(); return false; ">
            Изменить пароль
    </button>

<?endif; ?>
<?= $form->field($user, 'phone') ?>
<?php if ($role == 'client') {
    echo $form->field($user, 'card_number');
}
?>

<?php if ($isExpert): ?>
    <?= $form->field($user, 'company_posiotion') ?>
    <?php if (Yii::$app->user->identity->isAdmin)
        echo $form->field($user, 'admin', [
            'template' => '<div class="col-sm-12 checkbox">{input}{label}{error}<div><small class="text-muted">Администатор может видеть дела и задачи всех сотрудников. <br>Без этой опции будут права обычного специалиста, которому доступны только его дела и задачи.</small></div></div>',
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
    'cropPluginOptions' => [
            'minCropBoxWidth'=>0,
            'minCropBoxHeight'=>0,
    ],
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

