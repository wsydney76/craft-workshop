<?php

namespace project\modules\main\models;

use craft\elements\Asset;
use craft\elements\Entry;

class BackgroundImageModel extends BackgroundColorModel
{
    public $overlayTransparency = 50;
    public $useBlockImage = false;
    public $entryId = null;
    public $assetId = null;

    public function getAttr($addClass = [], $image = null)
    {

        $backgroundImage = null;

        // 1. Dynamic image
        if ($image && $this->useBlockImage) {
            $backgroundImage = $image;
        }

        if (!$backgroundImage && $this->entryId) {
            $entry = Entry::find()->id($this->entryId)->site('*')->unique()->one();
            if ($entry) {
                $backgroundImage = $entry->featuredImage->one();
            }
        }

        if (!$backgroundImage && $this->assetId) {
            $backgroundImage = Asset::findOne($this->assetId);
        }

        if ($backgroundImage) {
            $attr = parent::getAttr($addClass);
            $attr['style']['background-image'] .= ", url({$backgroundImage->getUrl(['width' => 2000])})";
        } else {
            $attr['class'] = $addClass;
        }

        return $attr;
    }

}
