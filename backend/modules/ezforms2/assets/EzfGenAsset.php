<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\modules\ezforms2\assets;

use yii\web\AssetBundle;

class EzfGenAsset extends AssetBundle
{
    public $sourcePath='@backend/modules/ezforms2/assets';

    public $css = [
        'css/condition.css'
    ];
    
    public $js = [
        'js/js-gen-condition.js?55599',
	//'js/appxqCore.js'
    ];

    public $depends = [
	
    ];
}
