<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Issues */

$this->title = 'Создать дело';

?>
<header class="section-header">
    <h3><?= Html::encode($this->title) ?></h3>
</header>
<div class="issues-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
