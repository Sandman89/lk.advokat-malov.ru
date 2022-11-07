<?php

namespace app\components\fullcalendar;

use Yii;
use yii\web\AssetBundle;

/**
 * @link http://www.frenzel.net/
 * @author Philipp Frenzel <philipp@frenzel.net> 
 */

class CoreAsset extends AssetBundle
{
    /**
     * [$sourcePath description]
     * @var string
     */
    public $sourcePath = '@bower/fullcalendar/dist';

    /**
     * the language the calender will be displayed in
     * @var string ISO2 code for the wished display language
     */
    public $language = NULL;

    /**
     * [$autoGenerate description]
     * @var boolean
     */
    public $autoGenerate = true;

    /**
     * tell the calendar, if you like to render google calendar events within the view
     * @var boolean
     */
    public $googleCalendar = false;
    
    /**
     * [$css description]
     * @var array
     */
    public $css = [
        'core/main.min.css',
        'daygrid/main.min.css',
        'timegrid/main.min.css'
    ];

    /**
     * [$js description]
     * @var array
     */
    public $js = [
        'core/main.js',        
        'daygrid/main.js',
        'timegrid/main.min.js',
        'interaction/main.js'

        //'core/locale-all.js',
    ];
    
    /**
     * [$depends description]
     * @var array
     */
    public $depends = [
        'yii\web\YiiAsset',
        //'yii2fullcalendar\PrintAsset'
    ];

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        $language = $this->language ? $this->language : Yii::$app->language;
        $tmp = explode('-', $language);
        $locale = strtolower($tmp[0]).'.js';//it; en;etc...
        $this->js[] = "core/locales/".$locale;
//
//        if($this->googleCalendar)
//        {
//            $this->js[] = 'gcal.js';
//        }

        parent::registerAssetFiles($view);
    }

}
