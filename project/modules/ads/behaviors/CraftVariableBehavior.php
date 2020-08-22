<?php

namespace project\modules\ads\behaviors;

use Craft;
use project\modules\ads\models\AdModel;
use yii\base\Behavior;

class CraftVariableBehavior extends Behavior
{
    public function ads(array $criteria = []) {
        $query = AdModel::find();
        Craft::configure($query, $criteria);
        return $query;
    }
}
