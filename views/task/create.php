<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Task */
/* @var $hidden_id_issue bool*/

$this->title = 'Создать задачу';
$this->params['breadcrumbs'][] = ['label' => 'Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-create">

    <header class="section-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </header>

    <?= $this->render('_form', [
        'model' => $model,
        'hidden_id_issue' => $hidden_id_issue
    ]) ?>

</div>
