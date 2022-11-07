<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TaskSearch */
/* @var bool $is_archive true - если вызван из экшена АрхивЗадач */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-search">

    <?php $form = ActiveForm::begin([
        'id' => 'task-search-form',
        'action' => ($is_archive) ? ['archive'] : ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <?= $form->field($model, 'dates_filter_result')->radioList($model->dates_filter, [
        'item' => function ($index, $label, $name, $checked, $value) {
            return
                '<label for="check-det-' . $index . '" class="btn btn-info-outline btn-radio-options ' . (($checked) ? 'active' : '') . '">
								' . Html::radio($name, $checked, ['value' => $value, 'id' => 'check-det-' . $index . '']) . $label . '		
								</label>';
        },
        'unselect' => null,
        'data-toggle' => "buttons",
        'onchange' => "$('#task-search-form').submit();"
    ])->label(false); ?>


    <div class="form-group hidden">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
