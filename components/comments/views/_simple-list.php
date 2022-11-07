<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $model app\components\comments\models\CommentModel $commentModel */
/* @var $maxLevel null|integer comments max level */
/* @var $statusWorkObject string */
?>


<li class="comment comment-level-<?php echo $model->level; ?>" id="comment-<?php echo $model->id; ?>">
    <div class="comment-content" data-comment-content-id="<?php echo $model->id ?>">
        <div class="comment-author-avatar">
            <?php echo Html::img($model->getAvatar(), ['alt' => $model->getAuthorName()]); ?>
        </div>
        <div class="comment-details">
            <div class="comment-action-buttons">
                <?php if ($statusWorkObject != 'completed') : ?>
                    <?php if ($model->isOwner()) : ?>
                        <?php echo Html::a('<span class="font-icon font-icon-trash"></span> ' . Yii::t('yii2mod.comments', 'Delete'), '#', ['data' => ['action' => 'delete', 'url' => Url::to(['/comment/delete', 'id' => $model->id]), 'comment-id' => $model->id]]); ?>
                        <?php echo Html::a('<span class="glyphicon glyphicon-pencil "></span> Редактировать', ['/comment/update', 'id' => $model->id], ['class' => 'lo-modal lo-modal-edit']); ?>
                    <?php endif; ?>
                    <?php if (!Yii::$app->user->isGuest && ($model->level < $maxLevel || is_null($maxLevel))) : ?>
                        <?php echo Html::a("<span class='glyphicon glyphicon-share-alt'></span> " . Yii::t('yii2mod.comments', 'Reply'), '#', ['class' => 'comment-reply', 'data' => ['action' => 'reply', 'comment-id' => $model->id]]); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="comment-author-name">
                <span><?php echo $model->getAuthorName(); ?></span>
                <span class="comment-date"><?= $model->getPostedDate() ?></span>
            </div>
            <div class="comment-body">
                <?php echo $model->getContent(); ?>
                <?php if ($model->files) : ?>
                    <div class="attachments">
                        <?php foreach ($model->files as $file) : ?>
                            <div class="attachments-element">
                                <i class="font-icon font-icon-attachment-type font-icon-page">
                                    <span class="attachments-element_ext ext_<?php echo $file->ext; ?>"><?php echo $file->ext; ?></span>
                                </i>
                                <div class="attachments-element_name"><?php echo $file->original_name; ?></div>
                                <div class="attachments-element_link">
                                    <a href="<?= $file->path; ?>" download data-pjax=0>Скачать</a>
                                    <?php echo \app\models\Filemanager::getLinkFileType($file) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</li>



