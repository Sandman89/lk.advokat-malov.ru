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
    if (Yii::$app->user->identity->isClient)
        $menu = ['items' => [
            ['label' => 'Моe дело', 'template' => '<a href="{url}" > <i class="glyphicon glyphicon-briefcase"></i><span class="lbl">{label}</span></a>', 'url' => ['/issues/index']],
            Yii::$app->user->isGuest ? (
            ['label' => 'Вход', 'template' => '<a href="{url}" > <i class="glyphicon glyphicon-log-in"></i><span class="lbl">{label}</span></a>', 'url' => ['/user/login']]
            ) : (
            ['label' => 'Выход', 'template' => '<a href="{url}" data-method="post"> <i class="glyphicon glyphicon-log-out"></i><span class="lbl">{label}</span></a>', 'url' => ['/site/logout']]
            ),
        ], 'options' => ['class' => 'side-menu-list'],
            'itemOptions' => ['class' => 'grey'],
            'activeCssClass' => 'opened'];
    else
        $menu = ['items' => [
            ['label' => 'Мои дела', 'template' => '<a href="{url}" > <i class="glyphicon glyphicon-briefcase"></i><span class="lbl">{label}</span></a>', 'url' => ['/issues/index']],
            ['label' => 'Мои задачи', 'template' => '<a href="{url}" > <i class="font-icon font-icon-notebook"></i><span class="lbl">{label}</span></a>', 'url' => ['/task/index']],
            ['label' => 'Архив', 'template' => '<span> <i class=" font-icon font-icon-archive"></i><span class="lbl">{label}</span></span>',
                'items' => [
                    ['label' => 'Архив задач', 'template' => '<a href="{url}" > <span class="lbl">{label}</span></a>', 'url' => ['/task/archive']],
                    ['label' => 'Архив дел', 'template' => '<a href="{url}" > <span class="lbl">{label}</span></a>', 'url' => ['/issues/archive']],
                ],
                'options' => ['class' => 'grey with-sub']
            ],
            ['label' => 'Создать', 'template' => '<span> <i class="glyphicon glyphicon-plus"></i><span class="lbl">{label}</span></span>',
                'items' => [
                    ['label' => 'Создать дело', 'template' => '<a href="{url}" > <span class="lbl">{label}</span></a>', 'url' => ['/issues/create']],
                    ['label' => 'Создать задачу', 'template' => '<a href="{url}" > <span class="lbl">{label}</span></a>', 'url' => ['/task/create']],
                ],
                'options' => ['class' => 'grey with-sub']
            ],
            ['label' => 'Клиенты', 'template' => '<a href="{url}" > <i class="font-icon font-icon-contacts"></i><span class="lbl">{label}</span></a>', 'url' => ['/admin/index']],
            ['label' => 'Календарь', 'template' => '<a href="{url}" > <i class="glyphicon glyphicon-calendar"></i><span class="lbl">{label}</span></a>', 'url' => ['/calendar/index']],
            ['label' => 'Сотрудники', 'template' => '<a href="{url}" > <i class="font-icon font-icon-users"></i><span class="lbl">{label}</span></a>', 'url' => ['/admin/experts']],
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
