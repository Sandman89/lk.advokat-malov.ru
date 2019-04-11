<?php
namespace app\assets;

use yii\web\AssetBundle;


class AdminAsset extends AssetBundle
{
    public $sourcePath = '@app/assets';
    public $css = [
        'css/lib/bootstrap/bootstrap.min.css',
      //  'css/lib/font-awesome/font-awesome.min.css',
     //   'css/lib/jqueryui/jquery-ui.min.css',
   //     'css/separate/pages/widgets.min.css',
     //   'css/separate/vendor/lobipanel.min.css',
        'css/main.css',
        'fonts/startui.eot',
        'fonts/startui.svg',
        'fonts/startui.ttf',
        'fonts/startui.woff',
        'fonts/Proxima_Nova_Regular.woff2',
        'fonts/Proxima_Nova_Regular.eot',
        'fonts/Proxima_Nova_Regular.ttf',
        'fonts/Proxima_Nova_Regular.svg',
        'fonts/Proxima_Nova_Semibold.woff2',
        'fonts/Proxima_Nova_Semibold.eot',
        'fonts/Proxima_Nova_Semibold.ttf',
        'fonts/Proxima_Nova_Semibold.svg',
        'fonts/glyphicons-halflings-regular.woff2',
        'fonts/glyphicons-halflings-regular.eot',
        'fonts/glyphicons-halflings-regular.ttf',
        'fonts/glyphicons-halflings-regular.svg'
    ];
    public $js = [
        'js/popper.min.js',
        'js/bootstrap-filestyle.min.js',
       // 'js/tether.min.js',
       // 'js/bootstrap.min.js',
        //'js/jquery-ui.min.js',
      //  'js/lobipanel.min.js',
       'js/plugins.js',
        'js/app.js',

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}