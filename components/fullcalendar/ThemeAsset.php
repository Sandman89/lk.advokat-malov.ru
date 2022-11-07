<?php

namespace app\components\fullcalendar;

use Yii;
use yii\web\AssetBundle;

/**
 * @link http://www.frenzel.net/
 * @author Philipp Frenzel <philipp@frenzel.net> 
 */

class ThemeAsset extends AssetBundle
{   
    /**
     * [$depends description]
     * @var array
     */
    public $depends = [
        'yii\jui\JuiAsset'
    ];
}
