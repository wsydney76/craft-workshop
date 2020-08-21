<?php

namespace project\modules\ads\records;

use Craft;
use craft\db\ActiveRecord;
use craft\helpers\DateTimeHelper;
use DateTime;

/**
 *
 * @property int $id Ad ID
 * @property string $title Ad Heading
 * @property string $text Ad Body Text
 * @property string $email Ad Contact Email
 * @property string $type Ad Type [search/offer]
 * @property string $status Ad Status [open/closed]
 * @property DateTime $dateCreatedLocal Ad Creation date/time in system timezone
 * @property DateTime $dateUpdatedLocal Ad last updated date/time in system timezone
 */
class AdsRecord extends ActiveRecord
{

    // Constants
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public static function tableName()
    {
        return '{{%app_ads}}';
    }


    public function rules()
    {
        return [
            [['title', 'text', 'email'], 'trim'],
            [['type', 'title', 'text', 'email', 'status'], 'required'],
            ['email', 'email'],
            ['title', 'string', 'length' => [4, 50]],
            ['text', 'string', 'length' => [20, 1000]],
            ['type', 'in', 'range' => ['search','offer']],
            ['status', 'in', 'range' => ['open','closed']]
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['type', 'title', 'email', 'text'];
        $scenarios[self::SCENARIO_UPDATE] = ['type', 'title', 'email', 'text', 'status'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'type' => Craft::t('ads', 'Type'),
            'title' => Craft::t('ads', 'Title'),
            'text' => Craft::t('ads', 'Text'),
            'email' => Craft::t('ads', 'Email'),
            'status' => Craft::t('ads', 'Status'),
        ];
    }

    public function dateCreatedLocal()
    {
        return DateTimeHelper::toDateTime($this->dateCreated);
    }
    public function dateUpdatedLocal()
    {
        return DateTimeHelper::toDateTime($this->dateUpdated);
    }
}
