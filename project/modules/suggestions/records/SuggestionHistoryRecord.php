<?php

namespace project\modules\suggestions\records;

use craft\db\ActiveRecord;
use craft\helpers\DateTimeHelper;
use DateTime;
use Exception;

/**
 * Class SuggestionRecord
 *
 * Represents a user entry suggestion
 *
 * @package project\modules\suggestions
 *
 * @property $suggestionId ID of suggestion record
 * @property $status Will be set to open when first saved, can be changed in CP
 * @property $notes Ongoing history of status changes
 * @property $relatedEntryId ID of the entry that fulfills this suggestion
 */
class SuggestionHistoryRecord extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%app_suggestions_history}}';
    }

    /**
     * Gets creation date in system time zone
     *
     * @return DateTime|false
     * @throws Exception
     */
    public function createdAt()
    {
        return DateTimeHelper::toDateTime($this->dateCreated);
    }

    public function getSuggestion()
    {
        return $this->hasOne(SuggestionRecord::class, ['id' => 'suggestionId']);
    }

}
