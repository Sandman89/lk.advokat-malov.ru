<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\IssuesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issues-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'created_at') ?>


    <?php // echo $form->field($model, 'parent') ?>

    <?php // echo $form->field($model, 'id_category') ?>

    <?php // echo $form->field($model, 'id_assign') ?>

    <?php // echo $form->field($model, 'id_client') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
