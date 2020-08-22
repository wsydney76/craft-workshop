<?php

namespace project\modules\ads\behaviors;

use Craft;
use project\modules\ads\records\AdRecord;
use yii\base\Behavior;

class CraftVariableBehavior extends Behavior
{
    public function ads(array $criteria = []) {
        $query = AdRecord::find();
        Craft::configure($query, $criteria);
        return $query;
    }
}
