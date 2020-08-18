<?php
/**
 * Created by PhpStorm.
 * User: wsydn
 * Date: 25.03.2019
 * Time: 13:04
 */

namespace project\resources\cp;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;
use function version_compare;

class CpAssets extends AssetBundle
{
    /** @inheritdoc */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/dist';

        $this->depends = [
            CpAsset::class
        ];

        $this->css = [
          'cp_styles.css'
        ];

        $this->js = [
          'cp_scripts.js'
        ];

        parent::init();
    }
}
