<?php

namespace project\modules\suggestions\models;

use craft\base\Model;
use craft\elements\Entry;
use craft\elements\User;
use project\modules\suggestions\records\db\SuggestionHistoryRecordQuery;
use project\modules\suggestions\records\SuggestionHistoryRecord;

class SuggestionHistoryModel extends Model
{
    public $id;
    public $suggestionId;
    public $status;
    public $title;
    public $notes;
    public $relatedEntryId;
    public $userId;
    public $creatorId;
    public $dateCreated;
    public $dateUpdated;
    public $uid;

    public static function find()
    {
        return new SuggestionHistoryRecordQuery(SuggestionHistoryRecord::class);
    }

    public function getEntry()
    {
        if (!$this->relatedEntryId) {
            return null;
        }
        return Entry::find()->anyStatus()->id($this->relatedEntryId)->one();
    }

    public function getUser()
    {
        if (!$this->userId) {
            return null;
        }
        return User::find()->id($this->userId)->one();
    }

    public function getCreator()
    {
        if (!$this->creatorId) {
            return null;
        }
        return User::find()->id($this->creatorId)->one();
    }

}
