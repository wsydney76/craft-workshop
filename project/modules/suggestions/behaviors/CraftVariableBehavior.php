<?php 

namespace project\modules\suggestions\behaviors;

use Craft;
use project\modules\suggestions\models\SuggestionModel;
use project\modules\suggestions\records\db\SuggestionRecordQuery;
use yii\base\Behavior;
use yii\base\InvalidConfigException;

class CraftVariableBehavior extends Behavior
{

    /**
     * @param array $criteria
     * @return SuggestionRecordQuery
     * @throws InvalidConfigException
     */
    public function getSuggestions(array $criteria = []): SuggestionRecordQuery
    {
        $query = SuggestionModel::find();
        Craft::configure($query, $criteria);
        return $query;
    }

}
