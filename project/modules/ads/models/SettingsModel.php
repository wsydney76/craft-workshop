<?php

namespace project\modules\ads\models;

use craft\base\Model;

class SettingsModel extends Model
{
    const PERPAGE = 3;
    const ACTIVEPERIOD = '-21 days';
    const MANAGEADSURL = 'ads/minimal'; // 'ads' or 'ads/minimal'
}
