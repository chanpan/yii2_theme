<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\modules\ezforms2\assets;

use yii\web\AssetBundle;

class IconAsset extends AssetBundle
{
    public $sourcePath='@backend/modules/ezforms2/assets';

    public $css = [
        'css/bootstrap-iconpicker.min.css',
    ];
    public $js = [
        'js/iconset/iconset-glyphicon.min.js',
        'js/iconset/iconset-fontawesome-4.2.0.min.js',
	'js/iconset/iconset-mapicon-2.1.0.min.js',
        'js/bootstrap-iconpicker.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
