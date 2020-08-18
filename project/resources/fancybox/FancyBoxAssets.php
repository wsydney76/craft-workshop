<?php

namespace project\resources\fancybox;

use craft\web\AssetBundle;
use yii\web\JqueryAsset;

class FancyBoxAssets extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = __DIR__ . '/dist';
        $this->depends = [
            JqueryAsset::class,
        ];
        $this->js = [
            'jquery.fancybox.min.js'
        ];
        $this->css = [
            'jquery.fancybox.min.css'
        ];
        parent::init();
    }
}
