<?php

/*
 * This file is part of the Dektrium project
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $user
 */
?>

<?php $this->beginContent('@app/views/admin/update.php', ['user' => $user]) ?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'options' => ['class' => 'form-horizontal','enctype' => 'multipart/form-data'],
    'fieldConfig' => [
        'options' => ['class' => 'form-group row'],
        'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper">{input}{error}</div></div>',
        'labelOptions' => ['class' => 'form-label'],
        'errorOptions' => ['class' => 'form-tooltip-error']
    ],
]); ?>
<div class="box-typical box-typical-padding">
    <?= $this->render('_user', ['form' => $form, 'user' => $user]) ?>

    <div class="form-group-buttons row">
        <div class="col-sm-12 text-center">
            <?= Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-success']) ?>
        </div>
    </div>
</div>



<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
