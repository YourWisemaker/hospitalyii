<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Font Awesome asset bundle.
 */
class FontAwesomeAsset extends AssetBundle
{
    public $sourcePath = '@npm/font-awesome';
    public $css = [
        'css/font-awesome.min.css',
    ];
}
