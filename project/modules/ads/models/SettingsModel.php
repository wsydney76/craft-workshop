<?php

namespace project\modules\ads\models;

use Craft;
use craft\base\Model;

class SettingsModel extends Model
{
    public $perPage = 10;
    public $activePeriod = '-21 days';
    public $manageAdsUrl = 'ads'; // 'ads' or 'ads/minimal'

    public function init()
    {
       $settings = Craft::$app->config->getConfigFromFile('ads');
        foreach ($settings as $key => $value) {
            $this->$key = $value;
       }
    }

}
