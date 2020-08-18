<?php

namespace apps\headless;

use craft\web\AssetBundle;


class HeadlessAssets extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = __DIR__ . '/dist';

        $this->css = [
            'app.css',
        ];

        $this->js = [
            'app.js'
        ];

        parent::init();
    }
}