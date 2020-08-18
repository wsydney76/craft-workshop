<?php

namespace project\modules\suggestions\records;

use Craft;
use craft\db\ActiveRecord;
use craft\db\SoftDeleteTrait;
use yii\db\ActiveQuery;

/**
 * Class SuggestionRecord
 *
 * Represents a user entry suggestion
 *
 * @package project\modules\suggestions
 *
 * @property string $name Name or pseudonym as entered in the suggestion form
 * @property string $email E-Mail as entered in the suggestion form
 * @property string $title
 * @property string $description
 * @property string $status Will be set to open when first saved, can be changed in CP
 * @property string $notes Ongoing history of status changes
 * @property mixed $history Gets related history records
 * @property string $site Site the suggestion was created from
 * @property $relatedEntryId ID of the entry that fulfills this suggestion
 * @property $dateCreated Date created in UTC
 * @property $dateDeleted Date the record was soft deleted
 */
class SuggestionRecord extends ActiveRecord
{
    use SoftDeleteTrait;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%app_suggestions}}';
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'title' => Craft::t('suggestions', 'Film Title'),
        ];
    }

    public function rules()
    {
        return [
            ['title', 'unique']
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getHistory(): ActiveQuery
    {
        return $this->hasMany(SuggestionHistoryRecord::class, ['suggestionId' => 'id'])->orderBy('dateCreated desc');
    }

}
