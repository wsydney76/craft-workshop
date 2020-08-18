<?php

namespace project\modules\suggestions\behaviors;

use craft\elements\Entry;
use project\modules\suggestions\models\SuggestionModel;
use project\modules\suggestions\records\db\SuggestionRecordQuery;
use yii\base\Behavior;

/**
 *
 * @property mixed $suggestions
 */
class EntryBehavior extends Behavior
{
    /**
     * @return SuggestionRecordQuery
     */
    public function getSuggestions()
    {
        /** @var Entry $entry */
        $entry = $this->owner;
        /** @var SuggestionRecordQuery $query */
        $query = SuggestionModel::find()
            ->entry($entry)
            ->orderBy('dateCreated desc');

        return $query;
    }
}
