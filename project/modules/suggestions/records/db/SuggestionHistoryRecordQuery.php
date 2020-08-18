<?php

namespace project\modules\suggestions\records\db;

use craft\helpers\DateTimeHelper;
use Exception;
use project\modules\suggestions\models\SuggestionHistoryModel;
use project\modules\suggestions\models\SuggestionModel;
use yii\db\ActiveQuery;

class SuggestionHistoryRecordQuery extends ActiveQuery
{
    /**
     * @param array $rows
     * @return array
     * @throws Exception
     */
    public function populate($rows)
    {
        $histories = [];
        foreach ($rows as $row) {
            $history = new SuggestionHistoryModel();
            $history->setAttributes($row, false);
            $history->dateCreated = DateTimeHelper::toDateTime($row['dateCreated']);
            $histories[] = $history;
        }
        return $histories;
    }

    public function suggestion(SuggestionModel $suggestion): SuggestionHistoryRecordQuery
    {
        if (!$suggestion) {
            return $this;
        }
        return $this->andWhere(['suggestionId' => $suggestion->id]);
    }
}
