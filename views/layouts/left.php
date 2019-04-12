<?php


use yii\helpers\Url;
use dektrium\user\Finder;
use yii\helpers\Html;
?>


<nav class="side-menu">
    <div class="side-menu-avatar">
        <a href="/user/account">
            <?php if (!Yii::$app->user->isGuest): ?>
            <div class="avatar-preview avatar-preview-100">

                    <?= Html::img(Yii::$app->user->identity->getImageSrc('small_')); ?>


            </div>
            <div class="avatar-preview avatar-preview-text text-center">
                <h5 class="avatar-preview-text__username"><?= Yii::$app->user->identity->getInitials() ?></h5>
                <span class="avatar-preview-text__email"><?= Yii::$app->user->identity->email ?></span>
            </div>

            <?php endif; ?>
        </a>
    </div>
    <?php

    $menu = ['items' => [
        ['label' => 'Мои дела', 'template' => '<a href="{url}" > <i class="glyphicon glyphicon-briefcase"></i><span class="lbl">{label}</span></a>', 'url' => ['/issues/index']],
        ['label' => 'Архив', 'template' => '<a href="{url}" > <i class=" font-icon font-icon-archive"></i><span class="lbl">{label}</span></a>', 'url' => ['/site/dela']],
        ['label' => 'Создать дело', 'template' => '<a href="{url}" > <i class="glyphicon glyphicon-plus"></i><span class="lbl">{label}</span></a>', 'url' => ['/issues/create']],
        ['label' => 'Клиенты', 'template' => '<a href="{url}" > <i class="font-icon font-icon-contacts"></i><span class="lbl">{label}</span></a>', 'url' => ['/admin/index']],
        ['label' => 'Календарь', 'template' => '<a href="{url}" > <i class="glyphicon glyphicon-calendar"></i><span class="lbl">{label}</span></a>', 'url' => ['/site/dela']],
        ['label' => 'Компания', 'template' => '<a href="{url}" > <i class="font-icon font-icon-build"></i><span class="lbl">{label}</span></a>', 'url' => ['/admin/experts']],
        ['label' => 'Настройки', 'template' => '<a href="{url}" > <i class="glyphicon glyphicon-cog"></i><span class="lbl">{label}</span></a>', 'url' => ['/site/settings']],
        Yii::$app->user->isGuest ? (
        ['label' => 'Вход', 'template' => '<a href="{url}" > <i class="glyphicon glyphicon-log-in"></i><span class="lbl">{label}</span></a>', 'url' => ['/user/login']]
        ) : (
        ['label' => 'Выход', 'template' => '<a href="{url}" data-method="post"> <i class="glyphicon glyphicon-log-out"></i><span class="lbl">{label}</span></a>', 'url' => ['/site/logout']]
        ),
    ], 'options' => ['class' => 'side-menu-list'],
        'itemOptions' => ['class' => 'grey'],
        'activeCssClass' => 'opened'];

    echo yii\widgets\Menu::widget($menu); ?>
</nav>
