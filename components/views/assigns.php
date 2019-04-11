<?php
/**
 * Created by PhpStorm.
 * User: fores
 * Date: 01.03.2019
 * Time: 2:42
 */

use yii\helpers\Html;

?>
<h5 class="m-b-md"><?= $title ?> </h5>
<div class="issues-view-assign box-typical p-a-lg p-t-0">
    <div class="row">
        <?php foreach ($users as $user_item): ?>
            <div class="col-md-4">
                <div class="issues-view-assign__item p-t-lg">
                    <div class="issues-view-assign__item-image p-t-lg"><?= Html::img($user_item->getImageSrc("small_")) ?></div>
                    <div class="issues-view-assign__item-text ">
                        <span class="inline-block grey-text m-b-8"><?= $user_item->company_posiotion ?></span><br>
                        <span class="inline-block"><?= $user_item->username ?></span>
                        <div >
                            <span class="inline-block p-y-8 m-r-md"> <i
                                        class="grey-text glyphicon glyphicon-earphone m-r-min font-14"></i> <?= $user_item->phone ?></span><br>
                            <span class="inline-block"> <i
                                        class="grey-text font-icon font-icon-mail m-r-min font-14"></i> <?= $user_item->email ?></span>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>