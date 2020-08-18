<?php

namespace project\modules\main\behaviors;

use craft\commerce\elements\Donation;
use yii\base\Behavior;

class LineItemBehavior extends Behavior
{
    public function isDonation() {
        return $this->owner->purchasable instanceof Donation;
    }
}
