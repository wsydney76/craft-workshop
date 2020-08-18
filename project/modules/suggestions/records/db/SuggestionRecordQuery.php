<?php

namespace project\modules\suggestions\records\db;

use craft\elements\Entry;
use craft\elements\User;
use craft\helpers\DateTimeHelper;
use Exception;
use project\modules\suggestions\models\SuggestionModel;
use yii\db\ActiveQuery;

/**
 * Class SuggestionRecordQuery
 *
 * @package project\modules\suggestions
 */
class SuggestionRecordQuery extends ActiveQuery
{

    /**
     * @var bool|null Whether to return trashed (soft-deleted) elements.
     * If this is set to `null`, then both trashed and non-trashed elements will be returned.
     * @used-by trashed()
     */
    public $trashed = false;

    /**
     * @inheritdoc
     * @uses $trashed
     */
    public function trashed($value = true)
    {
        $this->trashed = $value;
        return $this;
    }


    public function prepare($builder)
    {
        if ($this->trashed === false) {
            $this->andWhere(['dateDeleted' => null]);
        } else if ($this->trashed === true) {
            $this->andWhere(['not', ['dateDeleted' => null]]);
        }

        return parent::prepare($builder);
    }

    /**
     * @param array $rows
     * @return array
     * @throws Exception
     */
    public function populate($rows)
    {

        $suggestions = [];
        foreach ($rows as $row) {
            $suggestion = new SuggestionModel();
            $suggestion->setAttributes($row, false);
            $suggestion->dateCreated = DateTimeHelper::toDateTime($row['dateCreated']);
            $suggestions[] = $suggestion;
        }

        return $suggestions;
    }

    public function id($id): SuggestionRecordQuery
    {
        return $this->andWhere(['id' => $id]);
    }

    public function notId($id): SuggestionRecordQuery
    {
        return $this->andWhere(['<>', 'id', $id]);
    }

    public function status($status): SuggestionRecordQuery
    {
        return $this->andWhere(['status' => $status]);
    }

    public function entry(Entry $entry): SuggestionRecordQuery
    {
        if (!$entry) {
            return $this;
        }
        return $this->andWhere(['relatedEntryId' => $entry->id]);
    }

    public function uid($uid): SuggestionRecordQuery
    {
        if (!$uid) {
            return $this;
        }
        return $this->andWhere(['uid' => $uid]);
    }

    public function email($email): SuggestionRecordQuery
    {
        if (!$email) {
            return $this;
        }
        return $this->andWhere(['email' => $email]);
    }

    public function userId($userId): SuggestionRecordQuery
    {
        if (!$userId) {
            return $this;
        }
        return $this->andWhere(['userId' => $userId]);
    }

    public function relatedEntryId($entryId): SuggestionRecordQuery
    {
        if (!$entryId) {
            return $this;
        }
        return $this->andWhere(['relatedEntryId' => $entryId]);
    }

    public function user(User $user): SuggestionRecordQuery
    {
        if (!$user) {
            return $this;
        }
        return $this->andWhere(['userId' => $user->id]);
    }

    public function search($search): SuggestionRecordQuery
    {
        if (!$search) {
            return $this;
        }

        return $this->andWhere([
            'or',
            ['like', 'title', $search],
            ['like', 'name', $search],
            ['like', 'description', $search],
            ['like', 'notes', $search]
        ]);
    }
}
