<?php

namespace project\resources\site;

use Craft;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class SiteAssets extends AssetBundle
{
    /** @inheritdoc */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/dist';

        $this->depends = [
            JqueryAsset::class,
        ];

        $this->js = [
            'lib/bootstrap/bootstrap.bundle.min.js',
            'lib/gdpr/jquery.bs.gdpr.cookies.js',
            'scripts.js'
        ];

        $this->css = [
            Craft::$app->sites->currentSite->handle == 'kids' ?
                'styles_kids.css' :
                'styles.css'
        ];

        parent::init();
    }
}
