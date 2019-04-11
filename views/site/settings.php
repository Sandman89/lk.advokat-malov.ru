<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use kartik\tree\TreeView;

$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;
?>
<header class="section-header">
    <h3><?= Html::encode($this->title) ?></h3>
</header>
<div class="site-about">
    <div class="box-typica">
        <?php
        echo TreeView::widget([
            // single query fetch to render the tree
            'query' => app\models\Tree::find()->addOrderBy('root, lft'),
            'headingOptions' => ['label' => 'Категории судебной практики'],

            'rootOptions' => ['label' => '<span class="text-primary">Категории судебной практики</span>'],
            'topRootAsHeading' => true, // this will override the headingOptions
            'fontAwesome' => false,
            'isAdmin' => false,
            'iconEditSettings' => [
                'show' => 'none',
            ],
           // 'childNodeIconOptions' => ['class' => 'glyphicon glyphicon-plus'],
            'defaultExpandNodeIcon' => '<i class="glyphicon glyphicon-plus"></i>',
            'defaultCollapseNodeIcon' => '<i class="glyphicon glyphicon-minus"></i>',
            //'fontAwesome' => true,
            'softDelete' => false,
            'cacheSettings' => ['enableCache' => true],
            'mainTemplate' => '<div class="row">
                                <div class="col-sm-6">
                                    {wrapper}
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="box-typical">
                                    {detail}
                                    </div>
                                </div>
                            </div>'
        ]);
        ?>
    </div>
</div>
