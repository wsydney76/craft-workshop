<?php

namespace project\modules\ads\models;

use Craft;
use craft\db\ActiveRecord;
use craft\helpers\DateTimeHelper;
use craft\helpers\UrlHelper;
use DateTime;
use project\modules\ads\AdsModule;
use project\modules\ads\models\queries\AdModelQuery;

/**
 *
 * @property int $id Ad ID
 * @property string $title Ad Heading
 * @property string $text Ad Body Text
 * @property string $email Ad Contact Email
 * @property string $type Ad Type [search/offer]
 * @property string $status Ad Status [open/closed]
 * @property-read  DateTime $dateCreatedLocal Ad Creation date/time in system timezone
 * @property-read  DateTime $dateUpdatedLocal Ad last updated date/time in system timezone
 * @property-read  string $url Frontend Url
 * @property-read  bool $isActive Ad has status = open and is not expired
 * @property-read  bool $isExpired Ad is expired
 */
class AdModel extends ActiveRecord
{

    // Constants
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public static function tableName()
    {
        return '{{%app_ads}}';
    }

    public static function find(): AdModelQuery
    {
        $query =  new AdModelQuery(AdModel::class);
        return $query->orderBy('dateCreated desc');
    }

    public function rules()
    {
        return [
            [['title', 'text', 'email'], 'trim'],
            [['type', 'title', 'text', 'email', 'status'], 'required'],
            ['email', 'email'],
            ['title', 'string', 'length' => [4, 50]],
            ['text', 'string', 'length' => [20, 1000]],
            ['type', 'in', 'range' => ['search', 'offer']],
            ['status', 'in', 'range' => ['open', 'closed']]
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

    public function url()
    {
        return UrlHelper::siteUrl('ads/' . $this->id);
    }

    public function isActive()
    {
        return $this->status == 'open' && ! $this->isExpired();
    }

    public function isExpired()
    {
        $settings = AdsModule::getInstance()->settings;
        return $this->dateCreated < date('Y-m-d', strtotime($settings->activePeriod));
    }

    public function getDateCreatedLocal()
    {
        return DateTimeHelper::toDateTime($this->dateCreated);
    }

    public function getDateUpdatedLocal()
    {
        return DateTimeHelper::toDateTime($this->dateUpdated);
    }
}
