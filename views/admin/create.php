<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Nav;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var dektrium\user\models\User $user
 */
if (Yii::$app->controller->route == "admin/create-expert")
    $this->title = 'Создать нового сотрудника';
else
    $this->title = 'Создать нового клиента';
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user'),]) ?>


<header class="section-header">
    <h3><?= Html::encode($this->title) ?></h3>
</header>

<div class="box-typical box-typical-padding">
    <div class="alert alert-info">
        <?= Yii::t('user', 'Credentials will be sent to the user by email') ?>.
        <?= Yii::t('user', 'A password will be generated automatically if not provided') ?>.
    </div>
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

    <?= $this->render('_user', ['form' => $form, 'user' => $user]) ?>

    <div class="form-group row">
        <div class="col-sm-12 text-center">
            <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-success']) ?><br>
        </div>
    </div>

    <?php ActiveForm::end(); ?>



