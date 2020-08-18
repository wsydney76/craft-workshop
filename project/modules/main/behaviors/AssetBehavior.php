<?php

namespace project\modules\main\behaviors;

use craft\elements\Asset;
use craft\helpers\UrlHelper;
use yii\base\Behavior;
use function time;

class AssetBehavior extends Behavior
{
    public function vUrl($transform = null)
    {
        /** @var Asset $asset */
        $asset = $this->owner;

        return UrlHelper::url($asset->getUrl($transform), ['v' => $asset->dateModified->getTimestamp()]);
    }
}
