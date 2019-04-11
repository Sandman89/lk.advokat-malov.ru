<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */


use kartik\growl\Growl;

/**
 * @var dektrium\user\Module $module
 */
?>

<?php if ($module->enableFlashMessages): ?>
    <div class="row">
        <div class="col-xs-12">
            <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
                <?php
                echo Growl::widget([
                    'type' => $type,
                    'title' => '<span data-notify="icon" class="font-icon font-icon-check-circle"></span>',
                    'body' => $message,
                    'showSeparator' => true,
                    'delay' => 1, //This delay is how long before the message shows
                    'pluginOptions' => [
                        'delay' => (!empty($message['duration'])) ? $message['duration'] : 3000, //This delay is how long the message shows for
                    ]
                ]);

                ?>
            <?php endforeach ?>
        </div>
    </div>
<?php endif ?>
