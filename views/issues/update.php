<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Issues */

$this->title = 'Редактировать дело: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Issues', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<header class="section-header">
    <h3><?= Html::encode($this->title) ?></h3>
</header>

<div class="issues-update">

      <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
