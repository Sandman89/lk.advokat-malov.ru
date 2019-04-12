<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii2mod\editable\Editable;

/* @var $this \yii\web\View */
/* @var $model app\components\comments\models\CommentModel $commentModel */
/* @var $maxLevel null|integer comments max level */
?>
<li class="comment" id="comment-<?php echo $model->id; ?>">
    <?php if ($model->level == 1) : ?>
        <div class="list-activity-date">
            <?php echo $model->getPostedDate(); ?>
        </div>
        <div class="workflow-content" data-comment-content-id="<?php echo $model->id ?>">
            <div class="">
                <div class="comment-action-buttons">
                    <?php if (Yii::$app->getUser()->can('admin')) : ?>
                        <?php echo Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('yii2mod.comments', 'Delete'), '#', ['data' => ['action' => 'delete', 'url' => Url::to(['/comment/default/delete', 'id' => $model->id]), 'comment-id' => $model->id]]); ?>
                    <?php endif; ?>
                    <?php if (!Yii::$app->user->isGuest && ($model->level < $maxLevel || is_null($maxLevel))) : ?>
                        <?php echo Html::a("<span class='font-icon font-icon-comment'></span> " . Yii::t('yii2mod.comments', 'Reply'), '#', ['class' => 'comment-reply', 'data' => ['action' => 'reply', 'comment-id' => $model->id]]); ?>
                    <?php endif; ?>
                    <?php echo Html::a('<span class="glyphicon glyphicon-pencil "></span> Редактировать',['/comment/update','id'=>$model->id],['class'=>'lo-modal']); ?>
                </div>
                <div class="comment-title">
                    <?php echo $model->title; ?>
                </div>
                <div class="workflow-body">
                    <?php //if (Yii::$app->getModule('comment')->enableInlineEdit && Yii::$app->getUser()->can('admin')): ?>
                    <?php /*echo Editable::widget([
                        'model' => $model,
                        'attribute' => 'content',
                        'url' => '/comment/default/quick-edit',
                        'options' => [
                            'id' => 'editable-comment-' . $model->id,
                        ],
                    ]);*/ ?>
                    <?php //else: ?>
                    <?php echo $model->getContent(); ?>
                    <?php //endif; ?>
                </div>

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
                                    <a target="_blank" href="https://view.officeapps.live.com/op/embed.aspx?src=<?= Url::base(TRUE).$file->path; ?>">Смотреть</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    <?php else: ?>
        <div class="comment-content" data-comment-content-id="<?php echo $model->id ?>">
            <div class="comment-author-avatar">
                <?php echo Html::img($model->getAvatar(), ['alt' => $model->getAuthorName()]); ?>
            </div>
            <div class="comment-details">
                <div class="comment-action-buttons">
                    <?php if (Yii::$app->getUser()->can('admin')) : ?>
                        <?php echo Html::a('<span class="glyphicon glyphicon-trash"></span> ' . Yii::t('yii2mod.comments', 'Delete'), '#', ['data' => ['action' => 'delete', 'url' => Url::to(['/comment/default/delete', 'id' => $model->id]), 'comment-id' => $model->id]]); ?>
                    <?php endif; ?>
                    <?php if (!Yii::$app->user->isGuest && ($model->level < $maxLevel || is_null($maxLevel))) : ?>
                        <?php echo Html::a("<span class='glyphicon glyphicon-share-alt'></span> " . Yii::t('yii2mod.comments', 'Reply'), '#', ['class' => 'comment-reply', 'data' => ['action' => 'reply', 'comment-id' => $model->id]]); ?>
                    <?php endif; ?>
                </div>
                <div class="comment-author-name">
                    <span><?php echo $model->getAuthorName(); ?></span>
                    <?php echo Html::a($model->getPostedDate(), $model->getAnchorUrl(), ['class' => 'comment-date']); ?>
                </div>
                <div class="comment-body">
                    <?php //if (Yii::$app->getModule('comment')->enableInlineEdit && Yii::$app->getUser()->can('admin')): ?>
                    <?php /*echo Editable::widget([
                        'model' => $model,
                        'attribute' => 'content',
                        'url' => '/comment/default/quick-edit',
                        'options' => [
                            'id' => 'editable-comment-' . $model->id,
                        ],
                    ]);*/ ?>
                    <?php //else: ?>
                    <?php echo $model->getContent(); ?>
                    <?php //endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</li>
<?php if ($model->hasChildren()) : ?>
    <ul class="children">
        <?php foreach ($model->getChildren() as $children) : ?>
            <li class="comment" id="comment-<?php echo $children->id; ?>">
                <?php echo $this->render('_list', ['model' => $children, 'maxLevel' => $maxLevel]) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
