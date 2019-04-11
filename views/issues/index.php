<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use lo\widgets\modal\ModalAjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\IssuesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Issues';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="issues-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['id' => 'issues-pjax', 'timeout' => 5000,]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'linkOptions' => [
                'class'=>'page-link'
            ],
            'disabledListItemSubTagOptions' => ['tag' => 'span', 'class' => 'page-link'],
            'prevPageLabel' => 'prev',
            'nextPageLabel' => 'next',
            'pageCssClass' => 'paginate_button page-item',
            'nextPageCssClass' => 'paginate_button page-item next',    // Set CSS class for the “next” page button
            'prevPageCssClass' => 'paginate_button page-item previous',    // Set CSS class for the “previous” page button
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'title:ntext',
            'description:ntext',
            'created_at',
            //'estimate_at',
            //'parent',
            //'id_category',
            //'id_assign',
            //'id_client',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
