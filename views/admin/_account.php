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
 * @var bool $role
 * @var dektrium\user\models\User $user
 */
?>


<?php if ($user->scenario != 'settings') {
    $this->beginContent('@app/views/admin/update.php', ['user' => $user]);
} else {
    $this->title = Yii::t('user', 'Account settings');
    $this->params['breadcrumbs'][] = $this->title;
    $this->render('/_alert', ['module' => Yii::$app->getModule('user')]);
}
?>

<?php if ($user->scenario == 'settings'): ?>
    <header class="section-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </header>
<?php endif ?>
<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
    'fieldConfig' => [
        'options' => ['class' => 'form-group row'],
        'template' => '<div class="col-sm-12">{label}<div class="form-control-wrapper">{input}{error}</div></div>',
        'labelOptions' => ['class' => 'form-label'],
        'errorOptions' => ['class' => 'form-tooltip-error']
    ],
]); ?>
<div class="box-typical box-typical-padding">
    <?= $this->render('_user', ['form' => $form, 'user' => $user,'role'=>$role]) ?>

    <div class="form-group-buttons row">
        <div class="col-sm-12 text-center">
            <?= Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-success']) ?>
        </div>
    </div>
</div>


<?php ActiveForm::end(); ?>

<?php if ($user->scenario != 'settings') $this->endContent() ?>
