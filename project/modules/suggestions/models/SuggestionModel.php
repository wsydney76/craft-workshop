<?php

namespace project\modules\suggestions\models;

use Craft;
use craft\base\ElementInterface;
use craft\elements\Entry;
use craft\elements\User;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;
use project\modules\suggestions\records\db\SuggestionRecordQuery;
use project\modules\suggestions\records\SuggestionRecord;
use project\modules\suggestions\SuggestionModule;
use yii\base\Exception;

/**
 *
 * @property Entry|null|array|ElementInterface $entry
 * @property string $cpEditUrl
 * @property mixed $history
 * @property string $statusLabel
 * @property string $url
 */
class SuggestionModel extends BaseModel
{

    // Attributes
    public $id;
    public $title;
    public $name;
    public $email;
    public $description;
    public $notes;
    public $site;
    public $status = 'open';
    public $relatedEntryId;
    public $userId;
    public $dateCreated;
    public $dateUpdated;
    public $dateDeleted;
    public $uid;

    // Constants
    const SCENARIO_CREATE = 'create';

    /**
     * @return SuggestionRecordQuery
     */
    public static function find(): SuggestionRecordQuery
    {
        return new SuggestionRecordQuery(SuggestionRecord::class);
    }

    public static function findOne($id){
        return self::find()->id($id)->one();
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Craft::t('suggestions', 'ID'),
            'name' => Craft::t('suggestions', 'Name'),
            'title' => Craft::t('suggestions', 'Film Title'),
            'email' => Craft::t('suggestions', 'Email'),
            'description' => Craft::t('suggestions', 'Description'),
            'status' => Craft::t('suggestions', 'Status'),
            'notes' => Craft::t('suggestions', 'Notes'),
            'relatedEntryId' => Craft::t('suggestions', 'Entry'),
            'userId' => Craft::t('suggestions', 'Assigned User'),
            'dateCreated' => Craft::t('suggestions', 'Date'),
            'dateDeleted' => Craft::t('suggestions', 'Date deleted'),
            'site' => Craft::t('suggestions', 'Created from site'),
        ];
    }

    /**
     * Sets the allowed attributes for massive assignments in a given scenario
     *
     * @return array
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['name', 'email', 'title', 'description','site'];
        return $scenarios;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'email', 'title', 'description'], 'trim'],
            [['name', 'email', 'title', 'description'], 'required'],
            [['name', 'email', 'title', 'description'], 'sanitizeHtml'],
            ['email', 'email'],
            ['status', 'checkUserId']
        ];
    }

    /**
     * @param $attribute
     */
    public function sanitizeHtml($attribute)
    {
        if ($this->$attribute != StringHelper::stripHtml($this->$attribute)) {
            $this->addError($attribute, Craft::t('suggestions','HTML Tags are not allowed here'));
        }
    }

    /**
     *
     */
    public function checkUserId()
    {
        if ($this->status == 'atwork') {
            if (!$this->userId) {
                $this->addError('userId', Craft::t('suggestions','A user must be assigned for status At Work'));
            } else {
                $user = User::find()->id($this->userId)->anyStatus()->one();
                if ($user && ! $user->can('viewSuggestions')) {
                    $this->addError('userId', Craft::t('suggestions','This user does not have sufficient permissions'));
                }
            }
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getUrl(): string
    {
        return UrlHelper::siteUrl("suggestion/{$this->uid}");
    }

    /**
     * @return string
     */
    public function getCpEditUrl(): string
    {
        return UrlHelper::cpUrl("suggestions/{$this->id}");
    }

    public function isTrashed()
    {
        return (bool) $this->dateDeleted;
    }


    /**
     * @return string
     */
    public function getStatusLabel(): string
    {
        return Craft::t('suggestions', SuggestionModule::$suggestionStatus[$this->status]);
    }

    /**
     * @return array|ElementInterface|Entry|null
     */
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

    public function getHistory() {
        return SuggestionHistoryModel::find()->suggestion($this)->orderBy('id desc')->all();
    }


}
